<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attribute extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'attributes';
    
    protected $fillable = [
        'status',
        'order',
        'user_id',
        'category_id'
    ];

    public function languages()
    {
        return $this->belongsToMany(Language::class,
            'attribute_language',  'attribute_id','language_id')
            ->withPivot('name', 
                        'description',
                        'canonical',)
            ->withTimestamps();
    }

    public function attribute_category(){
        return $this->belongsToMany(AttributeCategory::class,'attribute_category_attribute','attribute_id',
        'attribute_category_id');
    }

    public function attribute_language()
    {
        return $this->hasMany(AttributeLanguage::class, 'attribute_id', 'id');
    }

    public function router()
    {
        return $this->hasOne(Router::class, 'module_id')
                    ->where('controller', 'App\Http\Controllers\Frontend\AttributeController');
    }

    protected static function booted()
    {
        
        // Khi xóa Post
        static::deleting(function ($attribute) {
            // Xóa router (forceDelete luôn nếu không cần soft delete)
            $attribute->router()->delete();

            // Nếu xóa mềm → cũng xóa mềm PostLanguage
            if (!$attribute->isForceDeleting()) {
                $attribute->attribute_language()->delete();
            }
        });

        // Khi khôi phục Post → khôi phục PostLanguage
        static::restoring(function ($attribute) {
            $attribute->attribute_language()->restore();
        });
    }
}