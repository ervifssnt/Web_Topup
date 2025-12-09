<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    // Show user dashboard/profile
    public function index()
    {
        $user = Auth::user();
        
        // Get transaction counts by status
        $totalOrders = $user->transactions()->count();
        $pendingOrders = $user->transactions()->where('status', 'pending')->count();
        $paidOrders = $user->transactions()->where('status', 'paid')->count();
        $failedOrders = $user->transactions()->where('status', 'failed')->count();
        
        // Get recent transactions
        $recentTransactions = $user->transactions()
            ->with('topupOption.game')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        return view('profile.index', compact(
            'user',
            'totalOrders',
            'pendingOrders',
            'paidOrders',
            'failedOrders',
            'recentTransactions'
        ));
    }
    
    // Show transaction history (Riwayat)
    public function history()
    {
        $user = Auth::user();

        // Get all transactions with pagination
        $transactions = $user->transactions()
            ->with('topupOption.game')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('profile.history', compact('transactions'));
    }

    // Update profile
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:20',
        ]);

        // Sanitize inputs
        $validated['username'] = \App\Helpers\InputSanitizer::sanitizeUsername($validated['username']);
        $validated['email'] = \App\Helpers\InputSanitizer::sanitizeEmail($validated['email']);
        $validated['phone'] = \App\Helpers\InputSanitizer::sanitizePhone($validated['phone']);

        $user->update($validated);

        // Log the action
        \App\Models\AuditLog::create([
            'user_id' => $user->id,
            'action' => 'profile_updated',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('profile.dashboard')->with('success', 'Profile updated successfully!');
    }
}