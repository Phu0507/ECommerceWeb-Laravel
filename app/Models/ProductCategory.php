<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'product_category';
    
    protected $fillable = [
        'parent_id',
        'lft',
        'rgt',
        'level',
        'image',
        'status',
        'order',
        'user_id'
    ];

    public function languages()
    {
        return $this->belongsToMany(Language::class,
            'product_category_language', 'product_category_id', 'language_id')
            ->withPivot('name', 
                        'description',
                        'canonical',
                        'content')
            ->withTimestamps();
    }

    public function product_category_language()
    {
        return $this->hasMany(ProductCategoryLanguage::class, 'product_category_id', 'id');
    }

    public function router()
    {
        return $this->hasOne(Router::class, 'module_id')
                    ->where('controller', 'App\Http\Controllers\Frontend\ProductCategoryController');
    }

    public function promotionProducts()
    {
        return $this->hasMany(PromotionProduct::class, 'category_id');
    }

    protected static function booted()
    { 
        static::deleting(function ($productCategory) {
            // Xóa router (forceDelete luôn nếu không cần soft delete)
            $productCategory->router()->delete();
        });
    }
}