<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = [
        'customer_id', 'session_id', 'promotion_id',
        'subtotal', 'promotion_discount', 'total',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }
}