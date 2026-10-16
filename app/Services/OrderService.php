<?php
namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderService
{
    protected $cartService;
    protected $productService;

    public function __construct(CartService $cartService, ProductService $productService)
    {
        $this->cartService = $cartService;
        $this->productService = $productService;
    }

    public function createOrder(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $cart = $this->cartService->getCart();
            if ($cart->items->isEmpty()) {
                throw new \Exception("Giỏ hàng trống, không thể tạo đơn hàng.");
            }
            $fullAddress = trim(($data['address'] ?? '') . ', ' .
                                ($data['ward'] ?? '') . ', ' .
                                ($data['district'] ?? '') . ', ' .
                                ($data['province'] ?? ''));

            $customerId = Auth::guard('customer')->id();
            $sessionId = $cart->session_id ?? session()->get('cart_session_id');
            
            $order = Order::create([
                'customer_id' => $customerId ?: null,
                'session_id'  => $customerId ? null : $sessionId,
                'customer_name'    => $data['name'],
                'customer_phone'   => $data['phone'],
                'customer_email'   => $data['email'] ?? null,
                'customer_address' => $fullAddress,

                'subtotal'           => $cart->subtotal,
                'promotion_discount' => $cart->promotion_discount,
                'coupon_discount'    => 0,
                'total'              => $cart->total,
                'note'            => $data['note'] ?? null,
                'payment_method'  => $data['payment_method'] ?? 'cod',
                'payment_status'  => 'unpaid',
                'order_status'    => 'pending',
            ]);

            foreach ($cart->items as $item) {
                OrderDetail::create([
                    'order_id'          => $order->id,
                    'product_id'        => $item->product_id,
                    'product_variant_id'=> $item->product_variant_id,
                    'quantity'          => $item->quantity,
                    'price'             => $item->price,
                    'discount'          => $item->discount,
                    'final_price'       => $item->final_price,
                ]);
            }
            $this->cartService->clearCart();
            return $order;
        });
    }

    public function updateOrderStatus(Order $order, string $status): Order
    {
        $order->update([
            'order_status' => $status,
        ]);
        return $order;
    }

    public function updatePaymentStatus(Order $order, string $status): Order
    {
        $order->update([
            'payment_status' => $status,
        ]);
        return $order;
    }

    public function getOrdersWithDetails()
    {
        if ($customerId = Auth::guard('customer')->id()) {
            return Order::with('orderDetails.product.translation', 'orderDetails.variant.translation')
                        ->where('customer_id', $customerId)
                        ->orderByDesc('created_at')
                        ->get();
        }

        $sessionId = session()->get('cart_session_id');
        if (!$sessionId) return collect();

        return Order::with('orderDetails.product.translation', 'orderDetails.variant.translation')
                    ->where('session_id', $sessionId)
                    ->orderByDesc('created_at')
                    ->get();
    }

    public function getOrdersWithFilter($request)
    {
        $perPage = $request->get('perpage', 10);
        $query = Order::query();
        if ($request->filled('order_status')) {
            $query->where('order_status', $request->order_status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'LIKE', "%{$search}%")
                  ->orWhere('customer_name', 'LIKE', "%{$search}%");
            });
        }
        return $query->orderByDesc('created_at')->paginate($perPage);
    }

    public function getOrderDetailById(int $id): ?Order
    {
        return Order::with([
            'orderDetails.product.translation',
            'orderDetails.variant.translation',
            'customer'
        ])->find($id);
    }

    public function mergeOrdersAfterLogin($customerId)
    {
        $sessionId = session()->get('cart_session_id');
        if (!$sessionId) {
            return;
        }
        $sessionOrders = Order::where('session_id', $sessionId)->get();
        foreach ($sessionOrders as $order) {
            if (!$order->customer_id) {
                $order->update([
                    'customer_id' => $customerId,
                    'session_id'  => null,
                ]);
            }
        }
    }

    public function reorder(int $orderId)
    {
        $order = $this->getOrderDetailById($orderId);
        if (!$order) {
            throw new \Exception("Đơn hàng không tồn tại");
        }
        foreach ($order->orderDetails as $detail) {
            $productData = $this->productService->getProductDetail($detail->product_id, false);
            if (!$productData) {
                continue;
            }
            $variantId = $detail->product_variant_id;
            $price= null;
            if ($variantId) {
                $index = collect($productData['variant_attributes'])
                            ->search(fn($attr) => $attr['id'] == $variantId);
                if ($index === false) {
                    continue;
                }
                $price = $productData['variant_price'][$index];
            } else {
                $price = $productData['price'] ?? $productData['base_price'];
            }

            $discount = 0;
            if (!empty($productData['promotions'])) {
                foreach ($productData['promotions'] as $promo) {
                    if ($promo['discount_type'] === 'percentage') {
                        $discount = max($discount, $price * ($promo['discounts'][0]['discount_value'] / 100));
                    } elseif ($promo['discount_type'] === 'fixed') {
                        $discount = max($discount, $promo['discounts'][0]['discount_value']);
                    }
                }
            }

            $finalPrice = max(0, $price - $discount);
            $this->cartService->addToCart(
                $productData['id'],
                $variantId,
                $detail->quantity,
                $price,
                $discount,
                $finalPrice
            );
        }
        return $this->cartService->getCart()->fresh('items');
    }

    public function getDashboardData()
    {
        // Doanh thu hôm nay (chỉ tính đơn hàng delivered)
        $todayRevenue = Order::where('order_status', 'delivered')
            ->whereDate('created_at', Carbon::today())
            ->sum('total');

        $yesterdayRevenue = Order::where('order_status', 'delivered')
            ->whereDate('created_at', Carbon::yesterday())
            ->sum('total');

        $todayChange = $yesterdayRevenue > 0
            ? (($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100
            : 0;

        // Doanh thu tháng này (chỉ tính đơn hàng delivered)
        $monthRevenue = Order::where('order_status', 'delivered')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total');

        $lastMonthRevenue = Order::where('order_status', 'delivered')
            ->whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->sum('total');

        $monthChange = $lastMonthRevenue > 0
            ? (($monthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100
            : 0;

        // Tổng số đơn hàng & khách hàng
        $totalOrders = Order::count();
        $totalCustomers = Customer::count();

        // 5 đơn hàng mới nhất
        $recentOrders = Order::latest()->take(5)->get();

        // Doanh thu theo tháng trong năm hiện tại (chỉ tính delivered)
        $months = [];
        $revenues = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[] = Carbon::create()->month($i)->format('M');
            $revenues[] = Order::where('order_status', 'delivered')
                ->whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', $i)
                ->sum('total');
        }

        return compact(
            'todayRevenue',
            'todayChange',
            'monthRevenue',
            'monthChange',
            'totalOrders',
            'totalCustomers',
            'recentOrders',
            'months',
            'revenues'
        );
    }

}