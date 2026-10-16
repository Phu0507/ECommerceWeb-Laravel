<?php

namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Province;
use App\Services\CartService;
use App\Services\CustomerService;
use App\Services\OrderService;
use App\Services\ProductCategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpdateProfileController extends Controller
{
    protected $cartService;
    protected $productCategoryService;
    protected $orderService;
    protected $customerService;

    public function __construct(CartService $cartService, ProductCategoryService $productCategoryService, OrderService $orderService, CustomerService $customerService)
    {
        $this->cartService = $cartService;
        $this->productCategoryService = $productCategoryService;
        $this->orderService= $orderService;
        $this->customerService = $customerService;
    }

    public function index(Request $request)
    {
        $productCategories = $this->productCategoryService->getAll();
        $cart = $this->cartService->getCart();
        $orders = $this->orderService->getOrdersWithDetails();
        $user = Auth::guard('customer')->user();
        $provinces = Province::all();
        // dd($cart);
        return view('frontend.updateProfile', compact('cart', 'productCategories', 'orders', 'user', 'provinces'));
    }

    public function update(Request $request)
    {
        $user = Auth::guard('customer')->user();
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'birthday' => 'nullable|date',
            'province_id' => 'nullable|string|max:255',
            'district_id' => 'nullable|string|max:255',
            'ward_id' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
        ]);

        $data = $request->only([
            'name', 'phone', 'birthday', 'province_id', 'district_id', 'ward_id', 'address'
        ]);
        $this->customerService->update($user->id, $data);
        return back()->with('success', 'Cập nhật thông tin thành công!');
    }
}