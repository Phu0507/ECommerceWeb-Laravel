<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttributeCategoryLanguage extends Model
{
    use HasFactory;
    protected $table = 'attribute_category_language';

    public function attributeCategory()
    {
        return $this->belongsTo(AttributeCategory::class, 'attribute_category_id', 'id');
    }
}