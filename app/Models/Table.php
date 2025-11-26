<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;

    protected $fillable = [
        'table_number',
        'qr_code_path',
        'status', // available, occupied
    ];

    /**
     * Relasi one-to-many ke Order.
     * Satu meja bisa memiliki banyak order (dine-in).
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}