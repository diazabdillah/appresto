<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price', // Harga yang tercatat saat pesanan dibuat
    ];

    /**
     * Relasi many-to-one ke Order.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relasi many-to-one ke Product.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}