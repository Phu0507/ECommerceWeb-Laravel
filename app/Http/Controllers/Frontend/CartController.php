<?php

namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\CartService;
use App\Services\ProductCategoryService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected $cartService;
    protected $productCategoryService;

    public function __construct(CartService $cartService, ProductCategoryService $productCategoryService)
    {
        $this->cartService = $cartService;
        $this->productCategoryService = $productCategoryService;
    }

    public function index(Request $request)
    {
        $productCategories = $this->productCategoryService->getAll();
        $cart = $this->cartService->getCart();
        // dd($cart);
        return view('frontend.cart', compact('cart', 'productCategories'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id'         => 'required|exists:products,id',
            'quantity'           => 'nullable|integer|min:1',
            'product_variant_id' => 'nullable|exists:product_variants,id',
            'price'              => 'required|numeric|min:0',
            'discount'           => 'required|numeric|min:0',
            'final_price'        => 'required|numeric|min:0',
        ]);

        $productId   = $request->input('product_id');
        $variantId   = $request->input('product_variant_id');
        $quantity    = $request->input('quantity', 1);
        $price       = $request->input('price');
        $discount    = $request->input('discount');
        $finalPrice  = $request->input('final_price');

        $cart = $this->cartService->addToCart($productId, $variantId, $quantity, $price, $discount, $finalPrice);

        return response()->json([
            'success' => true,
            'message' => 'Thêm vào giỏ hàng thành công',
            'cartCount' => $cart->items->count(),
            'cartTotal' => number_format($cart->total, 0, ',', '.') . '₫',
        ]);
    }

    public function updateItem(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:cart_items,id',
            'quantity' => 'required|integer|min:1',
        ]);
        $result = $this->cartService->updateItemQuantity($request->item_id, $request->quantity);
        if ($result) {
            return response()->json(array_merge(['success' => true], $result));
        }
        return response()->json(['success' => false], 404);
    }

    public function removeItem(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:cart_items,id',
        ]);
        $result = $this->cartService->removeItemById($request->item_id);
        if ($result) {
            return response()->json(array_merge(['success' => true], $result));
        }
        return response()->json(['success' => false], 404);
    }

}