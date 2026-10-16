<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromotionDiscount extends Model
{
    protected $table = 'promotion_discounts';
    protected $fillable = [
        'promotion_id', 'discount_value',
        'order_amount_from', 'order_amount_to',
        'min_quantity', 'max_quantity'
    ];

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }
}