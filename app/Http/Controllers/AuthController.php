<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AuditLog;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use PragmaRX\Google2FA\Google2FA;

class AuthController extends Controller
{
    protected $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    // Show register form
    public function showRegister()
    {
        return view('auth.register');
    }

    // Handle registration
    public function register(StoreUserRequest $request)
    {
        // Generate 2FA secret (REQUIRED for all users)
        $google2fa = app(\PragmaRX\Google2FA\Google2FA::class);
        $secret = $google2fa->generateSecretKey();

        // Create user
        $user = User::create([
            'username' => $request->username,
            'phone' => $request->phone,
            'email' => $request->email,
            'password_hash' => Hash::make($request->password),
        ]);

        // Set protected fields directly (bypassing mass assignment protection)
        $user->balance = 676767; // Initial balance
        $user->google2fa_secret = $secret;
        $user->google2fa_enabled = false; // Not enabled until verified
        $user->save();

        // Log registration
        AuditLog::log(
            'register',
            "New user registered: {$user->username} (2FA setup required)",
            'User',
            $user->id,
            null,
            ['username' => $user->username, 'phone' => $user->phone],
            $user->id // explicit user_id
        );

        // Login user temporarily
        Auth::login($user);

        // Redirect to mandatory 2FA setup
        return redirect()->route('2fa.setup.required')
            ->with('success', 'Account created! Please set up Two-Factor Authentication to secure your account.');
    }

    // Show login form
    public function showLogin()
    {
        return view('auth.login');
    }

    // Handle login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Rate limiting key
        $key = 'login:' . $request->email . '|' . $request->ip();

        // Check rate limit
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            $this->logLoginAttempt($request->email, false);
            
            $user = User::where('email', $request->email)->first();
            if ($user) {
                $user->incrementFailedAttempts();
            }
            
            throw ValidationException::withMessages([
                'email' => ["Too many login attempts. Please try again in {$seconds} seconds."],
            ]);
        }

        $user = User::where('email', $request->email)->first();

        // Check if account is locked
        if ($user && $user->isLocked()) {
            $this->logLoginAttempt($request->email, false);
            
            $minutesLeft = ceil(30 - now()->diffInMinutes($user->locked_at));
            
            throw ValidationException::withMessages([
                'email' => ["Account is locked due to too many failed login attempts. Try again in {$minutesLeft} minutes or contact administrator."],
            ]);
        }

        // Check credentials
        if ($user && Hash::check($request->password, $user->password_hash)) {
            // Check if 2FA is enabled
            if ($user->has2FAEnabled()) {
                // Store user ID in session for 2FA verification
                session(['2fa:user:id' => $user->id]);
                session(['2fa:remember' => $request->filled('remember')]);
                
                return redirect()->route('2fa.verify');
            }

            // No 2FA, login directly
            RateLimiter::clear($key);
            $this->logLoginAttempt($request->email, true);
            $user->resetFailedAttempts();
            
            AuditLog::log(
                'login',
                "User logged in: {$user->username}",
                'User',
                $user->id,
                null, // oldValues
                null, // newValues
                $user->id // explicit user_id
            );

            Auth::login($user, $request->filled('remember'));
            $request->session()->regenerate();

            return redirect()->intended(route('home'));
        }

        // Failed login
        RateLimiter::hit($key, 60);
        $this->logLoginAttempt($request->email, false);
        
        if ($user) {
            $user->incrementFailedAttempts();
        }

        return back()->withErrors([
            'email' => 'Invalid email or password.',
        ])->withInput($request->only('email'));
    }

    // Show 2FA verification page
    public function show2FAVerify()
    {
        if (!session()->has('2fa:user:id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-verify');
    }

    // Verify 2FA code
    public function verify2FA(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        if (!session()->has('2fa:user:id')) {
            return redirect()->route('login');
        }

        $user = User::findOrFail(session('2fa:user:id'));
        $code = str_replace(' ', '', $request->code);

        // Check if it's a recovery code (8 characters)
        if (strlen($code) === 8) {
            if ($user->useRecoveryCode($code)) {
                $this->complete2FALogin($user, $request);
                
                AuditLog::log(
                    '2fa_recovery_used',
                    "User logged in using recovery code: {$user->username}",
                    'User',
                    $user->id
                );
                
                return redirect()->route('home')
                    ->with('warning', 'You used a recovery code. Please regenerate your recovery codes.');
            }

            return back()->withErrors(['code' => 'Invalid recovery code.']);
        }

        // Verify TOTP code (6 digits)
        $valid = $this->google2fa->verifyKey($user->google2fa_secret, $code);

        if ($valid) {
            $this->complete2FALogin($user, $request);
            
            AuditLog::log(
                '2fa_login',
                "User logged in with 2FA: {$user->username}",
                'User',
                $user->id
            );
            
            return redirect()->intended(route('home'));
        }

        return back()->withErrors(['code' => 'Invalid verification code.']);
    }

    // Complete 2FA login
    private function complete2FALogin($user, $request)
    {
        $remember = session('2fa:remember', false);
        
        Auth::login($user, $remember);
        $request->session()->regenerate();
        
        // Clear 2FA session data
        session()->forget(['2fa:user:id', '2fa:remember']);
        
        $user->resetFailedAttempts();
    }

    // Handle logout
    public function logout(Request $request)
    {
        $username = Auth::user()->username;
        
        AuditLog::log(
            'logout',
            "User logged out: {$username}",
            'User',
            Auth::id()
        );
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    // Helper: Log login attempts
    private function logLoginAttempt($email, $successful)
    {
        DB::table('login_attempts')->insert([
            'email' => $email,
            'ip_address' => request()->ip(),
            'successful' => $successful,
            'user_agent' => request()->userAgent(),
            'attempted_at' => now(),
        ]);
    }
}