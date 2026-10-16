<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $fillable = [
        'name',
    ];

    protected $table = '2_devvn_quanhuyen';
}