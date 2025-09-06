<?php

// app/Models/ConfirmedOrder.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfirmedOrder extends Model
{
    protected $table = 'confirmed_orders';

    protected $fillable = [
        'order_id',
        'user_id',
        'payment_status',
        'payment_method',
        'delivery_address',
        'delivery_status',
        'tracking_number',
    ];
}
