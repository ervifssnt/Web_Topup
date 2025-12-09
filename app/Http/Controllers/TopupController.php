<?php

namespace App\Http\Controllers;

use App\Models\TopupRequest;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TopupController extends Controller
{
    // Show topup request form
    public function showForm()
    {
        $pendingRequests = TopupRequest::where('user_id', Auth::id())
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('topup.request', compact('pendingRequests'));
    }

    // Submit topup request
    public function submitRequest(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000',
            'payment_method' => 'required|string',
            'proof_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // 2MB max
            'notes' => 'nullable|string|max:500',
        ], [
            'amount.min' => 'Minimum top-up amount is Rp 10,000',
            'proof_image.image' => 'File must be an image',
            'proof_image.mimes' => 'Only JPG and PNG allowed',
            'proof_image.max' => 'Image must not exceed 2MB',
        ]);

        $proofPath = null;
        if ($request->hasFile('proof_image')) {
            // Verify MIME type (check actual file content, not just extension)
            $mimeType = $request->file('proof_image')->getMimeType();
            $allowedMimes = ['image/jpeg', 'image/png'];

            if (!in_array($mimeType, $allowedMimes)) {
                return back()->withErrors(['proof_image' => 'Invalid file type. Only JPG and PNG images are allowed.'])->withInput();
            }

            // Additional security: Check file size again
            if ($request->file('proof_image')->getSize() > 2048000) { // 2MB in bytes
                return back()->withErrors(['proof_image' => 'File size exceeds 2MB limit.'])->withInput();
            }

            // Security: Validate file extension matches MIME type
            $extension = strtolower($request->file('proof_image')->extension());
            $validExtensions = ['jpg', 'jpeg', 'png'];
            if (!in_array($extension, $validExtensions)) {
                return back()->withErrors(['proof_image' => 'Invalid file extension.'])->withInput();
            }

            // Generate cryptographically secure random filename (prevents directory traversal and collision)
            $secureFilename = hash('sha256', uniqid('', true) . time() . random_bytes(16)) . '.' . $extension;

            // Store in private directory (not publicly accessible)
            $proofPath = $request->file('proof_image')->storeAs('topup_proofs', $secureFilename, 'private');
        }

        $topupRequest = TopupRequest::create([
            'user_id' => Auth::id(),
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'proof_image' => $proofPath,
            'notes' => $request->notes,
            'status' => 'pending',
        ]);

        AuditLog::log(
            'topup_request',
            "User requested balance top-up: Rp " . number_format($topupRequest->amount, 0, ',', '.'),
            'TopupRequest',
            $topupRequest->id
        );

        return redirect()->route('topup.form')->with('success', 'Top-up request submitted! Please wait for admin approval.');
    }

    // User's topup history
    public function history()
    {
        $requests = TopupRequest::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('topup.history', compact('requests'));
    }
}