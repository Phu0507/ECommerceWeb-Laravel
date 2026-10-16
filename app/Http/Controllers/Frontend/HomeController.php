<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use App\Services\PostService;
use App\Services\ProductCategoryService;
use App\Services\ProductService;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    protected $productCategoryService;
    protected $productService;
    protected $postService;
    protected $cartService;

    public function __construct(ProductCategoryService $productCategoryService, ProductService $productService, PostService $postService, CartService $cartService)
    {
        $this->productCategoryService = $productCategoryService;
        $this->productService = $productService;
        $this->postService = $postService;
        $this->cartService = $cartService;
    }

    public function index()
    {
        $productCategories = $this->productCategoryService->getAll();
        $products = $this->productService->getLatest(8);
        $posts = $this->postService->getLatest(3);
        $cart = $this->cartService->getCart();
        // dd($posts);
        return view('frontend.homepage', compact('productCategories', 'products', 'posts', 'cart'));
    }
}