<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PasswordResetController extends Controller
{
    // Show forgot password form (standard Laravel flow)
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    // Send reset link (standard Laravel flow)
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Generic response to prevent user enumeration
        $genericMessage = 'If an account exists with this email, a password reset link will be sent.';

        $user = User::where('email', $request->email)->first();

        if ($user) {
            // Create password reset token
            $token = Str::random(60);

            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $request->email],
                [
                    'token' => Hash::make($token),
                    'created_at' => now(),
                ]
            );

            // Log the action
            AuditLog::log(
                'password_reset_link_requested',
                "Password reset link requested for: {$user->username}",
                'User',
                $user->id,
                null, // oldValues
                null, // newValues
                $user->id // explicit user_id
            );

            // SECURITY: Never log password reset tokens, passwords, or sensitive authentication tokens
            // Tokens are logged via AuditLog (without exposing the actual token value)
            // TODO: Send email with reset link (in production, this would be emailed)
        }

        return back()->with('success', $genericMessage);
    }

    // Show reset password form with token
    public function showResetForm($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    // Process password reset
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]+$/',
            ],
        ], [
            'password.regex' => 'Password must contain uppercase, lowercase, number, and special character.',
        ]);

        // Verify token
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$resetRecord) {
            return back()->withErrors(['email' => 'Invalid or expired reset token.']);
        }

        // Check if token is valid (Laravel uses Hash::check)
        if (!Hash::check($request->token, $resetRecord->token)) {
            return back()->withErrors(['email' => 'Invalid or expired reset token.']);
        }

        // Check if token is not expired (60 minutes)
        if (now()->diffInMinutes($resetRecord->created_at) > 60) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['email' => 'Reset token has expired. Please request a new one.']);
        }

        // Update password
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'User not found.']);
        }

        $user->password_hash = Hash::make($request->password);
        $user->save();

        // Delete the token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // Log the action
        AuditLog::log(
            'password_reset_completed',
            "Password reset completed for: {$user->username}",
            'User',
            $user->id,
            null, // oldValues
            null, // newValues
            $user->id // explicit user_id
        );

        return redirect()->route('login')->with('success', 'Password has been reset successfully! You can now login with your new password.');
    }

    // Simple password reset (verify identity with email + username + phone)
    public function simpleReset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'username' => 'required|string',
            'phone' => 'required|string',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]+$/',
            ],
        ], [
            'password.regex' => 'Password must contain uppercase, lowercase, number, and special character.',
        ]);

        // Find user by email, username, and phone
        $user = User::where('email', $request->email)
            ->where('username', $request->username)
            ->where('phone', $request->phone)
            ->first();

        if (!$user) {
            return back()->withErrors(['email' => 'The information provided does not match our records.'])->withInput();
        }

        // SECURITY: Require 2FA verification for password reset
        if (!$user->google2fa_enabled) {
            return back()->withErrors([
                'email' => 'Password reset requires two-factor authentication. Please contact support to enable 2FA on your account.',
            ])->withInput();
        }

        // Store user ID and new password in session for 2FA verification
        session([
            'password_reset_user_id' => $user->id,
            'password_reset_new_password' => $request->password,
            'password_reset_initiated_at' => now(),
        ]);

        // Redirect to 2FA verification page
        return redirect()->route('password.reset.verify2fa');
    }

    // Show 2FA verification form for password reset
    public function show2FAVerificationForm()
    {
        if (!session()->has('password_reset_user_id')) {
            return redirect()->route('password.request')
                ->withErrors(['error' => 'Password reset session expired. Please start over.']);
        }

        return view('auth.forgot-password-verify-2fa');
    }

    // Verify 2FA and complete password reset
    public function verify2FAForReset(Request $request)
    {
        $request->validate([
            '2fa_code' => 'required|string',
        ]);

        $userId = session('password_reset_user_id');
        $newPassword = session('password_reset_new_password');
        $initiatedAt = session('password_reset_initiated_at');

        if (!$userId || !$newPassword) {
            return back()->withErrors(['2fa_code' => 'Password reset session expired. Please start over.']);
        }

        // Check if session is older than 10 minutes
        if (now()->diffInMinutes($initiatedAt) > 10) {
            session()->forget(['password_reset_user_id', 'password_reset_new_password', 'password_reset_initiated_at']);
            return redirect()->route('password.request')
                ->withErrors(['error' => 'Password reset session expired. Please start over.']);
        }

        $user = User::find($userId);

        if (!$user || !$user->google2fa_enabled) {
            session()->forget(['password_reset_user_id', 'password_reset_new_password', 'password_reset_initiated_at']);
            return redirect()->route('password.request')
                ->withErrors(['error' => 'Invalid password reset session.']);
        }

        // Verify 2FA code
        $google2fa = app(\PragmaRX\Google2FA\Google2FA::class);
        $valid = $google2fa->verifyKey($user->google2fa_secret, $request->input('2fa_code'));

        if (!$valid) {
            return back()->withErrors(['2fa_code' => 'Invalid 2FA code. Please try again.']);
        }

        // Update password
        $user->password_hash = Hash::make($newPassword);
        $user->save();

        // Clear session
        session()->forget(['password_reset_user_id', 'password_reset_new_password', 'password_reset_initiated_at']);

        // Log the action
        AuditLog::log(
            'password_reset_simple',
            "Password reset via identity verification with 2FA: {$user->username}",
            'User',
            $user->id,
            null,
            null,
            $user->id
        );

        return redirect()->route('login')->with('success', 'Password has been reset successfully! You can now login with your new password.');
    }
}