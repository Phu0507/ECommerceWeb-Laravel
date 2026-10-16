<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ProductRating;
use App\Services\CartService;
use App\Services\ProductCategoryService;
use App\Services\ProductRatingService;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShopDetailController extends Controller
{

    protected $productCategoryService;
    protected $productService;
    protected $cartService;
    protected $productRatingService;

    public function __construct(ProductCategoryService $productCategoryService, ProductService $productService, CartService $cartService, ProductRatingService $productRatingService)
    {
        $this->productCategoryService = $productCategoryService;
        $this->productService = $productService;
        $this->cartService = $cartService;
        $this->productRatingService = $productRatingService;
    }

    public function index(Request $request, $id)
    {
        $productCategories = $this->productCategoryService->getAll();
        $cart = $this->cartService->getCart();
        $product = $this->productService->getProductDetail($id, true);
        $ratings = $this->productRatingService->getRatingsByProduct($id, 3);
        $ratingsQuery = ProductRating::where('product_id', $id);

        $avgRating = round($ratingsQuery->avg('rating'), 1);
        $starsCount = $ratingsQuery
            ->select('rating', DB::raw('count(*) as count'))
            ->groupBy('rating')
            ->pluck('count', 'rating') // trả về array: [rating => count]
            ->all();

        for($i=1; $i<=5; $i++){
            if(!isset($starsCount[$i])){
                $starsCount[$i] = 0;
            }
        }

        if (!$product) {
            abort(404);
        }
        return view('frontend.shop-details', compact('productCategories', 'product', 'cart', 'ratings', 'avgRating', 'starsCount'));
    }
    public function submitRating(Request $request, $productId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $result = $this->productRatingService->createRating(
            $productId,
            $request->rating,
            $request->comment
        );

        $starsCount = ProductRating::where('product_id', $productId)
            ->select('rating', DB::raw('count(*) as count'))
            ->groupBy('rating')
            ->pluck('count','rating')
            ->all();

        for($i = 1; $i<=5; $i++){
            if(!isset($starsCount[$i])){
                $starsCount[$i] = 0;
            }
        }

        $newReviewHtml = view('frontend.component.single_review', [
            'rating' => $result['rating']
        ])->render();

        $newRatingHtml = view('frontend.component.rating_summary', [
            'avgRating' => $result['avgRating'],
            'starsCount' => $starsCount
        ])->render();

        return response()->json([
            'success' => true,
            'newReviewHtml' => $newReviewHtml,
            'newRatingHtml' => $newRatingHtml,
        ]);
    }

}