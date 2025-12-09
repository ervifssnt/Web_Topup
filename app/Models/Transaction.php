<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'account_id',
        'topup_option_id',
        'coins',
        'price',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'coins' => 'integer',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function topupOption()
    {
        return $this->belongsTo(TopupOption::class);
    }

    public function game()
    {
        return $this->topupOption->game();
    }

    // Status helpers
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isPaid()
    {
        return $this->status === 'paid';
    }

    public function markAsPaid()
    {
        $this->status = 'paid';
        $this->save();
    }

    public function markAsFailed()
    {
        $this->status = 'failed';
        $this->save();
    }
}