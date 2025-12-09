<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'email',
        'phone',
        'password_hash',
    ];

    // Explicitly guard sensitive fields from mass assignment
    protected $guarded = [
        'balance',
        'is_admin',
        'is_locked',
        'locked_at',
        'locked_reason',
        'failed_login_attempts',
        'google2fa_secret',
        'google2fa_enabled',
        'recovery_codes',
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
        'google2fa_secret',
        'recovery_codes',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'google2fa_enabled' => 'boolean',
        'recovery_codes' => 'array',
    ];

    // Override default password column
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    // Relationships
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // Helper methods
    public function hasEnoughBalance($amount)
    {
        return $this->balance >= $amount;
    }

    public function deductBalance($amount)
    {
        $this->balance -= $amount;
        $this->save();
    }

    public function addBalance($amount)
    {
        $this->balance += $amount;
        $this->save();
    }

    /**
     * Atomically deduct balance with database-level validation.
     * Prevents race condition by using UPDATE with WHERE clause.
     * Returns true if successful, false if insufficient balance.
     */
    public function deductBalanceAtomic($amount)
    {
        // Use raw UPDATE query with balance check to ensure atomicity
        $affected = DB::table('users')
            ->where('id', $this->id)
            ->where('balance', '>=', $amount)
            ->update(['balance' => DB::raw("balance - {$amount}")]);

        if ($affected === 0) {
            return false; // Insufficient balance or concurrent transaction won the race
        }

        // Reload the model's balance from database
        $this->refresh();
        return true;
    }

    // Lock account
    public function lockAccount($reason = 'Too many failed login attempts')
    {
        $this->is_locked      = true;
        $this->locked_at      = now();
        $this->locked_reason  = $reason;
        $this->save();

        AuditLog::log(
            'account_locked',
            "Account locked: {$this->username} - Reason: {$reason}",
            'User',
            $this->id
        );
    }

    // Unlock account
    public function unlockAccount()
    {
        $this->is_locked             = false;
        $this->locked_at             = null;
        $this->locked_reason         = null;
        $this->failed_login_attempts = 0;
        $this->save();

        AuditLog::log(
            'account_unlocked',
            "Account unlocked: {$this->username}",
            'User',
            $this->id
        );
    }

    // Increment failed attempts
    public function incrementFailedAttempts()
    {
        $this->failed_login_attempts++;
        $this->save();

        // Lock after 5 failed attempts
        if ($this->failed_login_attempts >= 5) {
            $this->lockAccount();
        }
    }

    // Reset failed attempts
    public function resetFailedAttempts()
    {
        $this->failed_login_attempts = 0;
        $this->save();
    }

    // Check if account is locked
    public function isLocked()
    {
        // Auto-unlock after 30 minutes
        if ($this->is_locked && $this->locked_at) {
            if (now()->diffInMinutes($this->locked_at) >= 30) {
                $this->unlockAccount();
                return false;
            }
        }

        return $this->is_locked;
    }

    // 2FA Helper Methods
    public function has2FAEnabled()
    {
        return $this->google2fa_enabled && !empty($this->google2fa_secret);
    }

    public function generateRecoveryCodes()
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
        }
        return $codes;
    }

    public function useRecoveryCode($code)
    {
        $codes = $this->recovery_codes ?? [];
        $code = strtoupper($code);

        if (in_array($code, $codes)) {
            // Remove used code
            $codes = array_values(array_diff($codes, [$code]));
            $this->recovery_codes = $codes;
            $this->save();
            return true;
        }

        return false;
    }
}