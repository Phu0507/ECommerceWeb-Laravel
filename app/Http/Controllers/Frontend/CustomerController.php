<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerStoreRequest;
use App\Services\CartService;
use App\Services\CustomerService;
use App\Services\OrderService;
use App\Services\ProductCategoryService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CustomerController extends Controller
{

    protected $productCategoryService;
    protected $cartService;
    protected $customerService;
    protected $orderService;

    public function __construct(ProductCategoryService $productCategoryService, CartService $cartService, CustomerService $customerService, OrderService $orderService)
    {
        $this->productCategoryService = $productCategoryService;
        $this->cartService = $cartService;
        $this->customerService = $customerService;
        $this->orderService = $orderService;

        $this->middleware('guest:customer')->except('logout');
    }

    public function index()
    {
        $productCategories = $this->productCategoryService->getAll();
        $cart = $this->cartService->getCart();
        // dd($posts);
        return view('frontend.login', compact('productCategories', 'cart'));
    }

    public function postLogin(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('customer')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            $this->cartService->mergeCartAfterLogin(Auth::guard('customer')->id());
            $this->orderService->mergeOrdersAfterLogin(Auth::guard('customer')->id());
    
            return redirect()->intended('/')->with('success', 'Đăng nhập thành công!');
        }

        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu không đúng',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();
        $request->session()->regenerateToken();

        return redirect()->route('customer.index');
    }

    public function register()
    {
        $productCategories = $this->productCategoryService->getAll();
        $cart = $this->cartService->getCart();
        // dd($posts);
        return view('frontend.register', compact('productCategories', 'cart'));
    }

    public function store(CustomerStoreRequest $request)
    {
        $data = $request->validated();
        if ($data) {
           $this->customerService->create($data);
            return redirect()->route('customer.index')->with('success', 'Customer created successfully.');
        }
        return redirect()->back()->withErrors(['error' => 'Failed to create Customer.']);
    }
}