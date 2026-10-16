<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use App\Services\PostCatalogueService;
use App\Services\PostService;
use App\Services\ProductCategoryService;
use App\Services\ProductService;
use Illuminate\Http\Request;

class BlogController extends Controller
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

    public function index(Request $request)
    {
        $productCategories = $this->productCategoryService->getAll();
        $postCategories = $this->postCatalogueService->getAll();
        $perPage = $request->get('per_page', 3);
        $posts = $this->postService->getAllPageAndFilter($perPage);
        $latestPosts = $this->postService->getLatest(3);
        $cart = $this->cartService->getCart();
        // dd($posts);
        return view('frontend.blog', compact('productCategories', 'postCategories', 'posts', 'latestPosts', 'cart'));
    }
}