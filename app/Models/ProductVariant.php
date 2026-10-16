<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'product_variants';
    
    protected $fillable = [
        'product_id',
        'code',
        'quantity',
        'sku',
        'album',
        'status',
        'price',
        'user_id',
    ];

    public function languages()
    {
        return $this->belongsToMany(Language::class,
            'product_variant_language',  'product_variant_id','language_id')
            ->withPivot('name')
            ->withTimestamps();
    }

    public function product(){
        return $this->belongsTo(Product::class,'product_id',
        'id');
    }

    public function translation($languageId = null)
    {
        $languageId = $languageId ?? 7;
        return $this->hasOne(ProductVariantLanguage::class, 'product_variant_id', 'id')
                    ->where('language_id', $languageId);
    }

    public function attributes()
    {
        return $this->belongsToMany(Attribute::class, 'product_variant_attribute', 'product_variant_id', 'attribute_id')
                    ->withTimestamps();
    }
    
}