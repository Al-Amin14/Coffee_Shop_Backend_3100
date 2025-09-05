<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',        // <-- add this
        'amount',
        'session_id', 
        'status',
        'currency',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    // Optional: relation to user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Optional: relation to order items
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
