<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'session_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'customer_address',
        'subtotal',
        'promotion_discount',
        'coupon_discount',
        'total',
        'note',
        'payment_method',
        'payment_status',
        'order_status',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }
}