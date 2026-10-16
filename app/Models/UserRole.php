<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserRole extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'user_roles';

    protected $fillable = [
        'name',
        'is_active',
        'description'
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'role','id');
    }

    public function permissions()
    {
        return $this->belongsToMany(UserPermission::class, 'user_role_permission',
        'user_role_id', 'permission_id');
    }

    public function promotionCustomerConditions()
    {
        return $this->hasMany(PromotionCustomerCondition::class);
    }
}