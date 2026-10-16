<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttributeCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'attribute_category';
    
    protected $fillable = [
        'parent_id',
        'lft',
        'rgt',
        'level',
        'status',
        'user_id'
    ];

    public function languages()
    {
        return $this->belongsToMany(Language::class,
            'attribute_category_language', 'attribute_category_id', 'language_id')
            ->withPivot('name', 
                        'description',
                        'canonical')
            ->withTimestamps();
    }

    public function attribute_category_language()
    {
        return $this->hasMany(AttributeCategoryLanguage::class, 'attribute_category_id', 'id');
    }

    public function router()
    {
        return $this->hasOne(Router::class, 'module_id')
                    ->where('controller', 'App\Http\Controllers\Frontend\AttributeCategoryController');
    }

    protected static function booted()
    { 
        static::deleting(function ($attributeCategory) {
            // Xóa router (forceDelete luôn nếu không cần soft delete)
            $attributeCategory->router()->delete();
        });
    }
}