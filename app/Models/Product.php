<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'discount_price',
        'is_flash_sale',
        'flash_sale_end_at',
    ];
    
    protected $casts = [
        'is_flash_sale' => 'boolean',
        'flash_sale_end_at' => 'datetime',
    ];

    /**
     * Relasi many-to-one ke Category.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    
    /**
     * Relasi one-to-many ke OrderItem.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function testimonies()
    {
        return $this->hasMany(Testimony::class);
    }
    public function images()
{
    return $this->hasMany(ProductImage::class)->orderBy('is_main', 'desc')->orderBy('id', 'asc');
}
}