<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderService;
use App\Services\ProductCategoryService;
use Illuminate\Http\Request;

class OrderBEController extends Controller
{
     protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
        $this->middleware('auth:web');
    }

    public function index(Request $request)
    {
        $config = $this->config();
        $config['seo'] = config('apps.postcatalogue');
        $orders = $this->orderService->getOrdersWithFilter($request);
        return view('aadmin.order.order_index', compact('config', 'orders'));
    }

    public function show($id)
    {
        $config = $this->config();
        $config['seo'] = config('apps.postcatalogue');
        $order = $this->orderService->getOrderDetailById($id);
        if (!$order) {
            abort(404, 'Không tìm thấy đơn hàng');
        }
        // dd($order);
        return view('aadmin.order.order_detail_index', compact('config','order'));
    }

    private function config()
    {
        return [
            'js'=>[
                'css2/library/action.js',
            ],
            'css'=>['css2/css/plugins/switchery/switchery.css'],
        ];
    }

    public function updateStatus(Request $request, Order $order)
    {
        $status = $request->input('status');
        $validStatuses = ['pending', 'confirmed', 'shipping', 'delivered', 'cancelled'];
        if (!in_array($status, $validStatuses)) {
            return response()->json(['error' => 'Trạng thái không hợp lệ'], 400);
        }
        $order = $this->orderService->updateOrderStatus($order, $status);
        $statusBadge = view('aadmin.order.component.status_badge', compact('order'))->render();
        $actionsHtml = view('aadmin.order.component.actions', compact('order'))->render();
        return response()->json([
            'success' => true,
            'status_badge' => $statusBadge,
            'actions_html' => $actionsHtml
        ]);
    }

    public function getPaymentStatus(Order $order)
    {
        return response()->json([
            'success' => true,
            'status_badge' => $this->renderPaymentBadge($order->payment_status),
        ]);
    }

}