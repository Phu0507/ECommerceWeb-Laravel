<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'products';
    
    protected $fillable = [
        'image',
        'album',
        'status',
        'order',
        'user_id',
        'category_id',
        'base_price',
        'base_quantity',
    ];

    public function languages()
    {
        return $this->belongsToMany(Language::class,
            'product_language',  'product_id','language_id')
            ->withPivot('name', 
                        'description',
                        'canonical',
                        'meta_title')
            ->withTimestamps();
    }

    public function product_category(){
        return $this->belongsToMany(ProductCategory::class,'product_category_product','product_id',
        'product_category_id');
    }

    public function product_language()
    {
        return $this->hasMany(ProductLanguage::class, 'product_id', 'id');
    }

    public function product_variants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id', 'id');
    }

    public function router()
    {
        return $this->hasOne(Router::class, 'module_id')
                    ->where('controller', 'App\Http\Controllers\Frontend\ProductController');
    }

    public function promotionProducts()
    {
        return $this->hasMany(PromotionProduct::class);
    }

    public function ratings()
    {
        return $this->hasMany(ProductRating::class);
    }
    
    protected static function booted()
    {
        // Khi xóa Post
        static::deleting(function ($product) {
            // Xóa router (forceDelete luôn nếu không cần soft delete)
            $product->router()->delete();

            // Nếu xóa mềm → cũng xóa mềm PostLanguage
            if (!$product->isForceDeleting()) {
                $product->product_language()->delete();
            }
        });

        // Khi khôi phục Post → khôi phục PostLanguage
        static::restoring(function ($product) {
            $product->product_language()->restore();
        });
    }

    public function translation($languageId = null)
    {
        $languageId = $languageId ?? 7;
        return $this->hasOne(ProductLanguage::class, 'product_id')
                    ->where('language_id', $languageId);
    }

    public function getPriceInfo()
    {
        $promotion = $this->promotions->first(); 
        $discount = $promotion ? $promotion->discounts->first() : null;
        $discountPercent = $discount ? $discount->discount_value : 0;
        $finalPrice = $this->base_price;

        if ($discountPercent && $promotion) {
            if ($promotion->discount_type === 'percentage') {
                $finalPrice = $this->base_price * (1 - $discountPercent / 100);
            } elseif ($promotion->discount_type === 'fixed') {
                $finalPrice = $this->base_price - $discountPercent;
            }
        }
        return [
            'finalPrice'      => $finalPrice,
            'discountPercent' => $discountPercent,
            'hasDiscount'     => $this->promotions->isNotEmpty(),
            'promotion'       => $promotion,
        ];
    }

}