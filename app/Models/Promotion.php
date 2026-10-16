<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Promotion extends Model
{
    use HasFactory;

    protected $table = 'promotions';
    protected $fillable = [
        'name',
        'code',
        'description',
        'promotion_type',
        'discount_type',
        'customer_option',
        'start_date',
        'end_date'
    ];

    public function discounts()
    {
        return $this->hasMany(PromotionDiscount::class);
    }

    public function customerConditions()
    {
        return $this->hasMany(PromotionCustomerCondition::class);
    }

    public function products()
    {
        return $this->hasMany(PromotionProduct::class);
    }
}