<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use App\Services\ProductCategoryService;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ShopGridController extends Controller
{

    protected $productCategoryService;
    protected $productService;
    protected $cartService;

    public function __construct(ProductCategoryService $productCategoryService, ProductService $productService, CartService $cartService)
    {
        $this->productCategoryService = $productCategoryService;
        $this->productService = $productService;
        $this->cartService = $cartService;
    }

    public function index(Request $request)
    {
        $productCategories = $this->productCategoryService->getAll();
        $cart = $this->cartService->getCart();
        $perPage = $request->get('per_page', 9);
        $products = $this->productService->getAllPageAndFilter($perPage);
        // dd($products);
        return view('frontend.shop-grid', compact('productCategories', 'products', 'cart'));
    }
}