<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\User;
use App\Models\Transaction;
use App\Models\TopupOption;
use App\Models\AuditLog;
use App\Models\TopupRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreGameRequest;

class AdminController extends Controller
{
    // Dashboard
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_transactions' => Transaction::count(),
            'total_revenue' => Transaction::where('status', 'paid')->sum('price'),
            'pending_transactions' => Transaction::where('status', 'pending')->count(),
        ];
        
        $recent_transactions = Transaction::with('user', 'topupOption.game')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        
        return view('admin.dashboard', compact('stats', 'recent_transactions'));
    }
    
    // Games Management
    public function games()
    {
        $games = Game::withCount('topupOptions')->get();
        return view('admin.games.index', compact('games'));
    }
    
    public function createGame()
    {
        return view('admin.games.create');
    }
    
    public function storeGame(StoreGameRequest $request)
    {
        // Validation is automatic
        $game = Game::create($request->all());
        
        // Log action
        AuditLog::log(
            'create_game',
            "Created game: {$game->name}",
            'Game',
            $game->id,
            null,
            $game->toArray()
        );
        
        return redirect()->route('admin.games')->with('success', 'Game created successfully!');
    }
    
    public function editGame($id)
    {
        $game = Game::findOrFail($id);
        return view('admin.games.edit', compact('game'));
    }
    
    public function updateGame(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|string',
        ]);
        
        $game = Game::findOrFail($id);
        $oldValues = $game->toArray();
        $game->update($request->all());
        
        // Log action
        AuditLog::log(
            'update_game',
            "Updated game: {$game->name}",
            'Game',
            $game->id,
            $oldValues,
            $game->fresh()->toArray()
        );
        
        return redirect()->route('admin.games')->with('success', 'Game updated successfully!');
    }
    
    public function deleteGame($id)
    {
        $game = Game::findOrFail($id);
        $gameName = $game->name;
        $oldValues = $game->toArray();
        
        $game->delete();
        
        // Log action
        AuditLog::log(
            'delete_game',
            "Deleted game: {$gameName}",
            'Game',
            $id,
            $oldValues,
            null
        );
        
        return redirect()->route('admin.games')->with('success', 'Game deleted successfully!');
    }
    
    // Topup Options Management
    public function topupOptions()
    {
        $options = TopupOption::with('game')->orderBy('game_id')->get();
        $games = Game::all();
        return view('admin.topup-options.index', compact('options', 'games'));
    }
    
    public function createTopupOption()
    {
        $games = Game::all();
        return view('admin.topup-options.create', compact('games'));
    }
    
    public function storeTopupOption(Request $request)
    {
        $request->validate([
            'game_id' => 'required|exists:games,id',
            'coins' => 'required|integer',
            'amount' => 'required|string',
            'price' => 'required|numeric',
        ]);
        
        TopupOption::create($request->all());
        
        return redirect()->route('admin.topup-options')->with('success', 'Topup option created successfully!');
    }
    
    public function editTopupOption($id)
    {
        $option = TopupOption::findOrFail($id);
        $games = Game::all();
        return view('admin.topup-options.edit', compact('option', 'games'));
    }
    
    public function updateTopupOption(Request $request, $id)
    {
        $request->validate([
            'game_id' => 'required|exists:games,id',
            'coins' => 'required|integer',
            'amount' => 'required|string',
            'price' => 'required|numeric',
        ]);
        
        $option = TopupOption::findOrFail($id);
        $option->update($request->all());
        
        return redirect()->route('admin.topup-options')->with('success', 'Topup option updated successfully!');
    }
    
    public function deleteTopupOption($id)
    {
        $option = TopupOption::findOrFail($id);
        $option->delete();
        
        return redirect()->route('admin.topup-options')->with('success', 'Topup option deleted successfully!');
    }
    
    // Users Management
    public function users()
    {
        $users = User::withCount('transactions')->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.users.index', compact('users'));
    }
    
    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }
    
    public function updateUser(Request $request, $id)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'required|string',
            'balance' => 'required|numeric',
            'is_admin' => 'boolean',
        ]);

        $user = User::findOrFail($id);

        // Track old values for audit logging
        $oldBalance = $user->balance;
        $oldIsAdmin = $user->is_admin;

        // Explicitly update only allowed fields (prevents mass assignment vulnerabilities)
        $user->username = $validated['username'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'];

        // Handle balance change with audit logging
        if ($oldBalance != $validated['balance']) {
            $user->balance = $validated['balance'];

            AuditLog::log(
                'admin_balance_change',
                "Admin changed balance for {$user->username} from Rp " . number_format($oldBalance, 0, ',', '.') .
                " to Rp " . number_format($validated['balance'], 0, ',', '.'),
                'User',
                $user->id,
                ['balance' => $oldBalance],
                ['balance' => $validated['balance']],
                Auth::id()
            );
        }

        // Handle is_admin change with audit logging
        if ($request->has('is_admin') && $oldIsAdmin != $validated['is_admin']) {
            $user->is_admin = $validated['is_admin'];

            AuditLog::log(
                'admin_privilege_change',
                "Admin changed privilege for {$user->username} from " . ($oldIsAdmin ? 'Admin' : 'User') .
                " to " . ($validated['is_admin'] ? 'Admin' : 'User'),
                'User',
                $user->id,
                ['is_admin' => $oldIsAdmin],
                ['is_admin' => $validated['is_admin']],
                Auth::id()
            );
        }

        $user->save();

        return redirect()->route('admin.users')->with('success', 'User updated successfully!');
    }
    
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->is_admin) {
            return back()->with('error', 'Cannot delete admin user!');
        }
        
        $user->delete();
        
        return redirect()->route('admin.users')->with('success', 'User deleted successfully!');
    }
    
    // Transactions Management
    public function transactions()
    {
        $transactions = Transaction::with('user', 'topupOption.game')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('admin.transactions.index', compact('transactions'));
    }

    // Audit Logs
    public function auditLogs()
{
    $logs = AuditLog::with('user')
        ->orderBy('created_at', 'desc')
        ->paginate(50);
    
    return view('admin.audit-logs', compact('logs'));
}

public function unlockUser($id)
{
    $user = User::findOrFail($id);
    $user->unlockAccount();
    
    return redirect()->route('admin.users')->with('success', 'User account unlocked successfully!');
}

public function resetUserPassword(Request $request, $id)
{
    $request->validate([
        'new_password' => [
            'required',
            'string',
            'min:8',
            'confirmed',
            'regex:/[a-z]/',
            'regex:/[A-Z]/',
            'regex:/[0-9]/',
            'regex:/[@$!%*#?&]/',
        ],
    ], [
        'new_password.regex' => 'Password must contain uppercase, lowercase, number, and special character.',
    ]);
    
    $user = User::findOrFail($id);
    $oldEmail = $user->email ?? $user->phone;
    
    $user->password_hash = Hash::make($request->new_password);
    $user->save();
    
    // Log action
    AuditLog::log(
        'reset_user_password',
        "Admin reset password for user: {$user->username}",
        'User',
        $user->id
    );
    
    return redirect()->route('admin.users')->with('success', 'Password reset successfully!');
}

// Topup Requests Management
public function topupRequests()
{
    $requests = TopupRequest::with('user', 'processedBy')
        ->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected')")
        ->orderBy('created_at', 'desc')
        ->paginate(20);
        
    return view('admin.topup-requests.index', compact('requests'));
}

public function approveTopup(Request $request, $id)
{
    $topupRequest = TopupRequest::findOrFail($id);
    
    if ($topupRequest->status !== 'pending') {
        return back()->with('error', 'This request has already been processed.');
    }
    
    $request->validate([
        'admin_notes' => 'nullable|string|max:500',
    ]);
    
    DB::beginTransaction();
    
    try {
        // Add balance to user
        $user = $topupRequest->user;
        $user->addBalance($topupRequest->amount);
        
        // Update request status
        $topupRequest->update([
            'status' => 'approved',
            'processed_by' => Auth::id(),
            'admin_notes' => $request->admin_notes,
            'processed_at' => now(),
        ]);
        
        // Log action
        AuditLog::log(
            'approve_topup',
            "Approved top-up request for {$user->username}: Rp " . number_format($topupRequest->amount, 0, ',', '.'),
            'TopupRequest',
            $topupRequest->id
        );
        
        DB::commit();
        
        return redirect()->route('admin.topup-requests')->with('success', 'Top-up request approved and balance added!');
    } catch (\Exception $e) {
        DB::rollback();
        return back()->with('error', 'Failed to process top-up request.');
    }
}

public function rejectTopup(Request $request, $id)
{
    $topupRequest = TopupRequest::findOrFail($id);
    
    if ($topupRequest->status !== 'pending') {
        return back()->with('error', 'This request has already been processed.');
    }
    
    $request->validate([
        'admin_notes' => 'required|string|max:500',
    ]);
    
    $topupRequest->update([
        'status' => 'rejected',
        'processed_by' => Auth::id(),
        'admin_notes' => $request->admin_notes,
        'processed_at' => now(),
    ]);
    
    // Log action
    AuditLog::log(
        'reject_topup',
        "Rejected top-up request for {$topupRequest->user->username}: Rp " . number_format($topupRequest->amount, 0, ',', '.'),
        'TopupRequest',
        $topupRequest->id
    );
    
    return redirect()->route('admin.topup-requests')->with('success', 'Top-up request rejected.');
}

public function viewProof($id)
{
    $topup = TopupRequest::findOrFail($id);
    
    // Check if file exists
    if (!$topup->proof_image || !Storage::disk('private')->exists($topup->proof_image)) {
        abort(404, 'Proof image not found');
    }

    // Return file from private storage
    return response()->file(storage_path('app/private/' . $topup->proof_image));
}

// Password Reset Activity (Audit Logs)
public function passwordResetActivity()
{
    $resetLogs = \App\Models\AuditLog::where(function($query) {
        $query->where('action', 'LIKE', '%password_reset%')
              ->orWhere('action', 'LIKE', '%Password reset%')
              ->orWhere('description', 'LIKE', '%password reset%');
    })
    ->with('user')
    ->orderBy('created_at', 'desc')
    ->paginate(20);

    return view('admin.password-reset-activity', compact('resetLogs'));
}
}