<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'deposit_id',
        'product_name',
        'unit_price',
        'quantity'
    ];

    public function deposit()
    {
        return $this->belongsTo(Deposit::class);
    }
}
