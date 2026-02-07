<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = 'products';

    /**
     * The primary key associated with the table.
     * Your SQL schema defines this as 'id'.
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'product_name',
        'description',
        'category',
        'price',
        'discount',
        'stock_quantity',
        'unit',
        'is_available',
        'image_path',
        'created_at',
        'updated_at'
    ];

    /**
     * Scope a query to only include available products.
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', 1);
    }
}