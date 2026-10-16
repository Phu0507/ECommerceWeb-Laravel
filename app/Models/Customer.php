<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'phone',
        'role_id',
        'status',
        'province_id',
        'district_id',
        'ward_id',
        'address',
        'birthday',
        'image',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function role()
    {
        return $this->belongsTo(UserRole::class, 'role_id');
    }

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id', 'matp');
    }
    public function district()
    {
        return $this->belongsTo(District::class, 'district_id', 'maqh');
    }

    public function ward()
    {
        return $this->belongsTo(Ward::class, 'ward_id', 'xaid');
    }
}