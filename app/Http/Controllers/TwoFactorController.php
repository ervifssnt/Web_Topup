<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use App\Models\AuditLog;

class TwoFactorController extends Controller
{
    protected $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    // Show 2FA setup page
    public function show()
    {
        $user = auth()->user();
        
        return view('auth.two-factor', compact('user'));
    }

    // Enable 2FA - Generate secret and QR code
    public function enable()
    {
        $user = auth()->user();

        // Generate secret key
        $secret = $this->google2fa->generateSecretKey();
        
        // Save secret (not enabled yet)
        $user->google2fa_secret = $secret;
        $user->save();

        // Generate QR Code
        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email ?? $user->username,
            $secret
        );

        // Generate QR Code SVG
        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $qrCodeSvg = $writer->writeString($qrCodeUrl);

        return view('auth.two-factor-enable', [
            'qrCodeSvg' => $qrCodeSvg,
            'secret' => $secret,
            'user' => $user,
        ]);
    }

    // Verify and activate 2FA
    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:6',
        ]);

        $user = auth()->user();

        // Verify the code
        $valid = $this->google2fa->verifyKey($user->google2fa_secret, $request->code);

        if ($valid) {
            // Generate recovery codes
            $recoveryCodes = $user->generateRecoveryCodes();

            // Enable 2FA
            $user->google2fa_enabled = true;
            $user->recovery_codes = $recoveryCodes;
            $user->save();

            // Log action
            AuditLog::log(
                '2fa_enabled',
                "User enabled Two-Factor Authentication: {$user->username}",
                'User',
                $user->id
            );

            return redirect()->route('2fa.show')
                ->with('success', '2FA enabled successfully!')
                ->with('recovery_codes', $recoveryCodes);
        }

        return back()->withErrors(['code' => 'Invalid verification code. Please try again.']);
    }

    // Disable 2FA
    public function disable(Request $request)
    {
        $request->validate([
            'password' => 'required',
        ]);

        $user = auth()->user();

        // Verify password
        if (!\Hash::check($request->password, $user->password_hash)) {
            return back()->withErrors(['password' => 'Incorrect password.']);
        }

        // Disable 2FA
        $user->google2fa_enabled = false;
        $user->google2fa_secret = null;
        $user->recovery_codes = null;
        $user->save();

        // Log action
        AuditLog::log(
            '2fa_disabled',
            "User disabled Two-Factor Authentication: {$user->username}",
            'User',
            $user->id
        );

        return redirect()->route('2fa.show')
            ->with('success', '2FA has been disabled.');
    }

    // Show recovery codes
    public function showRecoveryCodes()
    {
        $user = auth()->user();

        if (!$user->has2FAEnabled()) {
            return redirect()->route('2fa.show')
                ->with('error', '2FA is not enabled.');
        }

        return view('auth.two-factor-recovery', [
            'user' => $user,
            'recoveryCodes' => $user->recovery_codes,
        ]);
    }

    // Regenerate recovery codes
    public function regenerateRecoveryCodes(Request $request)
    {
        $request->validate([
            'password' => 'required',
        ]);

        $user = auth()->user();

        // Verify password
        if (!\Hash::check($request->password, $user->password_hash)) {
            return back()->withErrors(['password' => 'Incorrect password.']);
        }

        // Generate new recovery codes
        $recoveryCodes = $user->generateRecoveryCodes();
        $user->recovery_codes = $recoveryCodes;
        $user->save();

        // Log action
        AuditLog::log(
            '2fa_recovery_regenerated',
            "User regenerated 2FA recovery codes: {$user->username}",
            'User',
            $user->id
        );

        return back()
            ->with('success', 'New recovery codes generated!')
            ->with('recovery_codes', $recoveryCodes);
    }

    // Show mandatory 2FA setup page (after registration)
    public function showMandatorySetup()
    {
        $user = auth()->user();

        // If 2FA already enabled, redirect to home
        if ($user->google2fa_enabled) {
            return redirect()->route('home');
        }

        // Generate QR Code
        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email ?? $user->username,
            $user->google2fa_secret
        );

        // Generate QR Code SVG
        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $qrCodeSvg = $writer->writeString($qrCodeUrl);

        return view('auth.two-factor-mandatory', [
            'qrCodeSvg' => $qrCodeSvg,
            'secret' => $user->google2fa_secret,
            'user' => $user,
        ]);
    }

    // Verify and complete mandatory 2FA setup
    public function verifyMandatorySetup(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:6',
        ]);

        $user = auth()->user();

        // Verify the code
        $valid = $this->google2fa->verifyKey($user->google2fa_secret, $request->code);

        if (!$valid) {
            return back()->withErrors(['code' => 'Invalid verification code. Please try again.']);
        }

        // Generate recovery codes
        $recoveryCodes = $user->generateRecoveryCodes();
        $user->recovery_codes = $recoveryCodes;
        $user->google2fa_enabled = true;
        $user->save();

        // Log action
        AuditLog::log(
            '2fa_enabled',
            "User enabled 2FA (mandatory setup): {$user->username}",
            'User',
            $user->id
        );

        return view('auth.two-factor-recovery-codes', [
            'recoveryCodes' => $recoveryCodes,
        ]);
    }
}