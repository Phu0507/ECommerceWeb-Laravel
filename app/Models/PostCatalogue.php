<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostCatalogue extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'parent_id',
        'lft',
        'rgt',
        'level',
        'image',
        'icon',
        'album',
        'publish',
        'order',
        'user_id'
    ];

    protected $table = 'post_catalogues';
    public function languages(){
    return $this->belongsToMany(Language::class,
        'post_catalogue_language', 'post_catalogue_id', 'language_id')
        ->withPivot('name', 
                    'description',
                    'canonical',
                    'meta_title', 
                    'meta_description', 
                    'meta_keyword',
                    'content')
        ->withTimestamps();
}

    public function post_catalogue_language()
    {
        return $this->hasMany(PostCatalogueLanguage::class, 'post_catalogue_id', 'id');
    }

    public function posts(){
        return $this->belongsToMany(Post::class,'post_catalogue_post',
        'post_catalogue_id','post_id');
    }

    public function router()
    {
        return $this->hasOne(Router::class, 'module_id')
                    ->where('controller', 'App\Http\Controllers\Frontend\PostCatalogueController');
    }

    protected static function booted()
    {
        
        // Khi xóa Post
        static::deleting(function ($postCatalogue) {
            // Xóa router (forceDelete luôn nếu không cần soft delete)
            $postCatalogue->router()->delete();
        });
    }

}