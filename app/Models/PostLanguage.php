<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostLanguage extends Model
{
    use HasFactory;
    protected $table = 'post_language';

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id', 'id');
    }
}