<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $fillable = [
        'name',
        'canonical',
        'image',
        'user_id',
        'status',
    ];

    protected $table = 'languages';

    public function postCatalogues()
    {
        return $this->belongsToMany(PostCatalogue::class,
            'post_catalogue_language', 'language_id', 'post_catalogue_id')
            ->withPivot('name', 
                        'description',
                        'canonical',
                        'meta_title', 
                        'meta_description', 
                        'meta_keyword',
                        'content')
            ->withTimestamps();
    }

    public function productCategory()
    {
        return $this->belongsToMany(ProductCategory::class,
            'product_category_language', 'language_id', 'product_category_id')
            ->withPivot('name', 
                        'description',
                        'canonical',
                        'content')
            ->withTimestamps();
    }
    
}