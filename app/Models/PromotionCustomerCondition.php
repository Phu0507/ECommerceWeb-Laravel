<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromotionCustomerCondition extends Model
{
    protected $table = 'promotion_customer_conditions';
    protected $fillable = [
        'promotion_id', 'gender', 'age_group', 'user_role_id'
    ];

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }

    public function userRole()
    {
        return $this->belongsTo(UserRole::class, 'user_role_id');
    }
}