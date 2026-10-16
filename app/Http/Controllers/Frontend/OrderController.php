<?php

namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\ProductCategoryService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $cartService;
    protected $productCategoryService;
    protected $orderService;

    public function __construct(CartService $cartService, ProductCategoryService $productCategoryService, OrderService $orderService)
    {
        $this->cartService = $cartService;
        $this->productCategoryService = $productCategoryService;
        $this->orderService= $orderService;
    }

    public function index(Request $request)
    {
        $productCategories = $this->productCategoryService->getAll();
        $cart = $this->cartService->getCart();
        $orders = $this->orderService->getOrdersWithDetails();
        // dd($cart);
        return view('frontend.order', compact('cart', 'productCategories', 'orders'));
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

    public function cancel(Order $order)
    {
        $this->orderService->updateOrderStatus($order, 'cancelled');
        return redirect()->back()->with('success', 'Đơn hàng đã được hủy thành công.');
    }

    public function reorder(Request $request, $orderId)
    {
        try {
            $cart = $this->orderService->reorder($orderId);

            return redirect()
                ->route('cart.index')
                ->with('success', "Đã thêm sản phẩm từ đơn hàng #{$orderId} vào giỏ hàng");
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

}