<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
        'unit_price',
        'total_price',
        'image_path',
        'product_name'
    ];

    // Relationship: A cart belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship: A cart belongs to a product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
