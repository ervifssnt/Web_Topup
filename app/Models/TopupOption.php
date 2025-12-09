<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TopupOption extends Model
{
    protected $fillable = [
        'game_id',
        'coins',
        'amount',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'coins' => 'integer',
    ];

    // Relationships
    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}