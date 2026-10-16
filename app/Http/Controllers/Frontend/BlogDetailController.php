<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use App\Services\PostCatalogueService;
use App\Services\PostService;
use App\Services\ProductCategoryService;
use App\Services\ProductService;
use Illuminate\Http\Request;

class BlogDetailController extends Controller
{

    protected $postCatalogueService;
    protected $productCategoryService;
    protected $postService;
    protected $cartService;

    public function __construct(PostCatalogueService $postCatalogueService, ProductCategoryService $productCategoryService, PostService $postService, CartService $cartService)
    {
        $this->postCatalogueService = $postCatalogueService;
        $this->productCategoryService = $productCategoryService;
        $this->postService = $postService;
        $this->cartService = $cartService;
    }

    public function index(Request $request, $category_canonical, $post_canonical)
    {
        $productCategories = $this->productCategoryService->getAll();
        $postCategories = $this->postCatalogueService->getAll();
        $cart = $this->cartService->getCart();
        
        $langId = 7;
        $post = $this->postService->getByCanonical($category_canonical,$post_canonical,$langId);
        $recentPosts = $this->postService->getLatest(3, $post->id, $langId);
        if ($post->category_canonical !== $category_canonical) {
            abort(404);
        }
        return view('frontend.blog-details', compact('productCategories', 'postCategories', 'post', 'recentPosts', 'cart'));
    }
}