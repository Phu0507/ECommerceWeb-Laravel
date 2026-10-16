<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'image',
        'icon',
        'album',
        'publish',
        'order',
        'user_id',
        'catalogue_id'
    ];

    protected $table = 'posts';

    public function languages(){
    return $this->belongsToMany(Language::class,
        'post_language',  'post_id','language_id')
        ->withPivot('name', 
                    'description',
                    'canonical',
                    'meta_title', 
                    'meta_description', 
                    'meta_keyword',
                    'content')
        ->withTimestamps();
    }

    public function post_catalogues(){
        return $this->belongsToMany(PostCatalogue::class,'post_catalogue_post','post_id',
        'post_catalogue_id');
    }

    public function post_language()
    {
        return $this->hasMany(PostLanguage::class, 'post_id', 'id');
    }

    public function router()
    {
        return $this->hasOne(Router::class, 'module_id')
                    ->where('controller', 'App\Http\Controllers\Frontend\PostController');
    }

    protected static function booted()
    {
        
        // Khi xóa Post
        static::deleting(function ($post) {
            // Xóa router (forceDelete luôn nếu không cần soft delete)
            $post->router()->delete();

            // Nếu xóa mềm → cũng xóa mềm PostLanguage
            if (!$post->isForceDeleting()) {
                $post->post_language()->delete();
            }
        });

        // Khi khôi phục Post → khôi phục PostLanguage
        static::restoring(function ($post) {
            $post->post_language()->restore();
        });
    }
    
}