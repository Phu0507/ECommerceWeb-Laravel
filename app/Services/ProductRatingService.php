<?php
namespace App\Services;

use App\Models\ProductRating;
use Illuminate\Support\Facades\Auth;

class ProductRatingService
{
    public function createRating($productId, $ratingValue, $comment = null)
    {
        $rating = ProductRating::create([
            'product_id' => $productId,
            'customer_id' => Auth::guard('customer')->id(),
            'rating' => $ratingValue,
            'comment' => $comment,
        ]);

        // Tính sao trung bình
        $avgRating = ProductRating::where('product_id', $productId)->avg('rating');
        $avgRating = round($avgRating, 1);

        return [
            'rating' => $rating,
            'avgRating' => $avgRating,
        ];
    }

    public function getRatingsByProduct($productId, $perPage = 5)
    {
        return ProductRating::with('customer')
            ->where('product_id', $productId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}