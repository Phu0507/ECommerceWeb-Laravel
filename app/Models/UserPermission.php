<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPermission extends Model
{
    use HasFactory;

    protected $table = 'permissions';

    protected $fillable = [
        'name',
        'canonical',
    ];

    public function roles()
    {
        return $this->belongsToMany(UserRole::class, 'user_role_permission', 'permission_id',
        'user_role_id');
    }
}