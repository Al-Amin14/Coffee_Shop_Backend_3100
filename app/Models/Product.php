<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class Product extends Model
{
    use HasFactory;
    protected $primaryKey = 'product_id';

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
    ];
}
