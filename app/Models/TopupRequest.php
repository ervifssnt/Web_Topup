<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TopupRequest extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'payment_method',
        'proof_image',
        'notes',
        'status',
        'processed_by',
        'admin_notes',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}