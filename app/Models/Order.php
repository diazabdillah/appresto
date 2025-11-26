<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'table_id',
        'order_type', // dine_in, delivery, takeaway
        'delivery_address',
        'total_amount',
        'customer_hp',
        'customer_name',
        'customer_email',
        'payment_status', // pending, paid, failed
        'midtrans_snap_token',
    ];

    /**
     * Relasi many-to-one ke User (nullable).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi many-to-one ke Table (nullable).
     */
    public function table()
    {
        return $this->belongsTo(Table::class);
    }
    
    /**
     * Relasi one-to-many ke OrderItem.
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}