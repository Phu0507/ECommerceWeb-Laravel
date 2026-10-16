<?php

namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\CartService;
use App\Services\CheckoutService;
use App\Services\OrderService;
use App\Services\ProductCategoryService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    protected $cartService;
    protected $productCategoryService;
    protected $checkoutService;
    protected $orderService;

    public function __construct(CartService $cartService, ProductCategoryService $productCategoryService, CheckoutService $checkoutService, OrderService $orderService)
    {
        $this->cartService = $cartService;
        $this->productCategoryService = $productCategoryService;
        $this->checkoutService = $checkoutService;
        $this->orderService = $orderService;
    }

    public function index(Request $request)
    {
        $productCategories = $this->productCategoryService->getAll();
        $cart = $this->cartService->getCart();
        // dd($cart);
        return view('frontend.checkout', compact('cart', 'productCategories'));
    }

    public function vnpayPayment(Request $request)
    {
        $order = $this->orderService->createOrder($request->all());
        if ($request->payment_method === 'cod') {
            $this->orderService->updatePaymentStatus($order, 'unpaid');
            $this->orderService->updateOrderStatus($order, 'pending');
            return redirect()->route('order.success', $order->id)
                ->with('success', 'Đặt hàng thành công. Vui lòng thanh toán khi nhận hàng.');
        }
        if ($request->payment_method === 'vnpay') {
            $this->orderService->updatePaymentStatus($order, 'pending');
            $this->orderService->updateOrderStatus($order, 'pending');
            $paymentUrl = $this->checkoutService->createPaymentUrl([
                'amount' => $order->total,
                'order_id' => $order->id,
            ]);

            return redirect()->to($paymentUrl);
        }
    }

    public function vnpayReturn(Request $request)
    {
        $orderId = $request->input('vnp_TxnRef');
        $order = Order::findOrFail($orderId);

        // Kiểm tra kết quả thanh toán
        if ($request->input('vnp_ResponseCode') == '00') {
            // Thành công
            $this->orderService->updatePaymentStatus($order, 'paid');
            return redirect()->route('order.success', $order->id)
                ->with('success', 'Thanh toán VNPay thành công.');
        } else {
            // Thất bại
            $this->orderService->updatePaymentStatus($order, 'failed');
            $this->orderService->updateOrderStatus($order, 'cancelled');
            return redirect()->route('order.failed', $order->id)
                ->with('error', 'Thanh toán VNPay thất bại.');
        }
    }

    public function success(Order $order)
    {
        $productCategories = $this->productCategoryService->getAll();
        $cart = $this->cartService->getCart();
        return view('frontend.component.order.success', compact('order','cart', 'productCategories'));
    }

    public function failed(Order $order)
    {
        $productCategories = $this->productCategoryService->getAll();
        $cart = $this->cartService->getCart();
        return view('frontend.component.order.failed', compact('order', 'cart','productCategories'));
    }

}