<?php

use App\Http\Controllers\AttributeCategoryController;
use App\Http\Controllers\AttributeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Frontend\BlogController;
use App\Http\Controllers\Frontend\BlogDetailController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\CheckoutController;
use App\Http\Controllers\Frontend\CustomerController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\PostCatalogueController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserPermissionController;
use App\Http\Controllers\UserRoleController;
use Illuminate\Support\Facades\Route;
//frontend
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\OrderController;
use App\Http\Controllers\Frontend\ShopDetailController;
use App\Http\Controllers\Frontend\ShopGridController;
use App\Http\Controllers\Frontend\UpdateProfileController;
use App\Http\Controllers\OrderBEController;

Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('login', [AuthController::class, 'postLogin'])->name('postLogin');
Route::get('logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth:web')->get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

/* User Management Routes */
Route::middleware('auth:web')->prefix('user')->name('user.')->group(function () {
    Route::get('index', [UserController::class, 'index'])->name('index');     // Danh sách
    Route::get('create', [UserController::class, 'create'])->name('create');  // Form thêm
    Route::post('store', [UserController::class, 'store'])->name('store');    // Lưu thêm mới
    Route::get('edit/{id}', [UserController::class, 'edit'])->name('edit');   // Form sửa
    Route::post('update/{id}', [UserController::class, 'update'])->name('update'); // Lưu sửa
    Route::delete('destroy/{id}', [UserController::class, 'destroy'])->name('destroy'); // Xóa
});

/* User Roles Management Routes */
Route::middleware('auth:web')->prefix('user/role')->name('user.role.')->group(function () {
    Route::get('index', [UserRoleController::class, 'index'])->name('index');     // Danh sách
    Route::get('create', [UserRoleController::class, 'create'])->name('create');  // Form thêm
    Route::post('store', [UserRoleController::class, 'store'])->name('store');    // Lưu thêm mới
    Route::get('edit/{id}', [UserRoleController::class, 'edit'])->name('edit');   // Form sửa
    Route::post('update/{id}', [UserRoleController::class, 'update'])->name('update'); // Lưu sửa
    Route::delete('destroy/{id}', [UserRoleController::class, 'destroy'])->name('destroy'); // Xóa
    Route::get('permission', [UserRoleController::class, 'permission'])->name('permission'); // phân quyền
    Route::post('UpdatePermission', [UserRoleController::class, 'UpdatePermission'])->name('UpdatePermission'); // phân quyền
});


/* User Permissions Management Routes */
Route::middleware('auth:web')->prefix('user/permission')->name('user.permission.')->group(function () {
    Route::get('index', [UserPermissionController::class, 'index'])->name('index');     // Danh sách
    Route::get('create', [UserPermissionController::class, 'create'])->name('create');  // Form thêm
    Route::post('store', [UserPermissionController::class, 'store'])->name('store');    // Lưu thêm mới
    Route::get('edit/{id}', [UserPermissionController::class, 'edit'])->name('edit');   // Form sửa
    Route::post('update/{id}', [UserPermissionController::class, 'update'])->name('update'); // Lưu sửa
    Route::delete('destroy/{id}', [UserPermissionController::class, 'destroy'])->name('destroy'); // Xóa
});

/*Ajax*/
Route::get('api/districts', [LocationController::class, 'getDistricts'])->name('ajax.districts');
Route::get('api/wards', [LocationController::class, 'getWards'])->name('ajax.wards');
Route::post('/update-status', [UserController::class, 'updateStatus'])->name('user.updateStatus');
Route::get('/get-attribute-values/{category}', [AttributeController::class, 'getValues']);
Route::get('/ajax-search-products', [ProductController::class, 'ajaxSearch'])->name('ajax.search.products');
Route::get('/ajax-get-categories', [ProductCategoryController::class, 'ajaxGetCategories'])->name('ajax.get.categories');
Route::get('/ajax-get-userRoles', [UserRoleController::class, 'getRoles'])->name('ajax.get.userRoles');

Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');

Route::post('/orders/{order}/status', [OrderBEController::class, 'updateStatus'])
    ->name('orders.updateStatus');

/* Language Management Routes */
Route::middleware('auth:web')->prefix('language')->name('language.')->group(function () {
    Route::get('index', [LanguageController::class, 'index'])->name('index');     // Danh sách
    Route::get('create', [LanguageController::class, 'create'])->name('create');  // Form thêm
    Route::post('store', [LanguageController::class, 'store'])->name('store');    // Lưu thêm mới
    Route::get('edit/{id}', [LanguageController::class, 'edit'])->name('edit');   // Form sửa
    Route::post('update/{id}', [LanguageController::class, 'update'])->name('update'); // Lưu sửa
    Route::delete('destroy/{id}', [LanguageController::class, 'destroy'])->name('destroy'); // Xóa
});

/* Post Catalogue Management Routes */
Route::middleware('auth:web')->prefix('post/catalogue')->name('post.catalogue.')->group(function () {
    Route::get('index', [PostCatalogueController::class, 'index'])->name('index');     // Danh sách
    Route::get('create', [PostCatalogueController::class, 'create'])->name('create');  // Form thêm
    Route::post('store', [PostCatalogueController::class, 'store'])->name('store');    // Lưu thêm mới
    Route::get('edit/{id}', [PostCatalogueController::class, 'edit'])->name('edit');   // Form sửa
    Route::post('update/{id}', [PostCatalogueController::class, 'update'])->name('update'); // Lưu sửa
    Route::delete('destroy/{id}', [PostCatalogueController::class, 'destroy'])->name('destroy'); // Xóa
});


/* Post Management Routes */
Route::middleware('auth:web')->prefix('post')->name('post.')->group(function () {
    Route::get('index', [PostController::class, 'index'])->name('index');     // Danh sách
    Route::get('create', [PostController::class, 'create'])->name('create');  // Form thêm
    Route::post('store', [PostController::class, 'store'])->name('store');    // Lưu thêm mới
    Route::get('edit/{id}', [PostController::class, 'edit'])->name('edit');   // Form sửa
    Route::post('update/{id}', [PostController::class, 'update'])->name('update'); // Lưu sửa
    Route::delete('destroy/{id}', [PostController::class, 'destroy'])->name('destroy'); // Xóa
});


/* Product Category Management Routes */
Route::middleware('auth:web')->prefix('product/category')->name('product.category.')->group(function () {
    Route::get('index', [ProductCategoryController::class, 'index'])->name('index');     // Danh sách
    Route::get('create', [ProductCategoryController::class, 'create'])->name('create');  // Form thêm
    Route::post('store', [ProductCategoryController::class, 'store'])->name('store');    // Lưu thêm mới
    Route::get('edit/{id}', [ProductCategoryController::class, 'edit'])->name('edit');   // Form sửa
    Route::post('update/{id}', [ProductCategoryController::class, 'update'])->name('update'); // Lưu sửa
    Route::delete('destroy/{id}', [ProductCategoryController::class, 'destroy'])->name('destroy'); // Xóa
});

/* Product Management Routes */
Route::middleware('auth:web')->prefix('product')->name('product.')->group(function () {
    Route::get('index', [ProductController::class, 'index'])->name('index');     // Danh sách
    Route::get('create', [ProductController::class, 'create'])->name('create');  // Form thêm
    Route::post('store', [ProductController::class, 'store'])->name('store');    // Lưu thêm mới
    Route::get('edit/{id}', [ProductController::class, 'edit'])->name('edit');   // Form sửa
    Route::post('update/{id}', [ProductController::class, 'update'])->name('update'); // Lưu sửa
    Route::delete('destroy/{id}', [ProductController::class, 'destroy'])->name('destroy'); // Xóa
});

/* Attribute Category Management Routes */
Route::middleware('auth:web')->prefix('attribute/category')->name('attribute.category.')->group(function () {
    Route::get('index', [AttributeCategoryController::class, 'index'])->name('index');     // Danh sách
    Route::get('create', [AttributeCategoryController::class, 'create'])->name('create');  // Form thêm
    Route::post('store', [AttributeCategoryController::class, 'store'])->name('store');    // Lưu thêm mới
    Route::get('edit/{id}', [AttributeCategoryController::class, 'edit'])->name('edit');   // Form sửa
    Route::post('update/{id}', [AttributeCategoryController::class, 'update'])->name('update'); // Lưu sửa
    Route::delete('destroy/{id}', [AttributeCategoryController::class, 'destroy'])->name('destroy'); // Xóa
});


/* Attribute Management Routes */
Route::middleware('auth:web')->prefix('attribute')->name('attribute.')->group(function () {
    Route::get('index', [AttributeController::class, 'index'])->name('index');     // Danh sách
    Route::get('create', [AttributeController::class, 'create'])->name('create');  // Form thêm
    Route::post('store', [AttributeController::class, 'store'])->name('store');    // Lưu thêm mới
    Route::get('edit/{id}', [AttributeController::class, 'edit'])->name('edit');   // Form sửa
    Route::post('update/{id}', [AttributeController::class, 'update'])->name('update'); // Lưu sửa
    Route::delete('destroy/{id}', [AttributeController::class, 'destroy'])->name('destroy'); // Xóa
});

/* Promotion Management Routes */
Route::middleware('auth:web')->prefix('promotion')->name('promotion.')->group(function () {
    Route::get('index', [PromotionController::class, 'index'])->name('index');     // Danh sách
    Route::get('create', [PromotionController::class, 'create'])->name('create');  // Form thêm
    Route::post('store', [PromotionController::class, 'store'])->name('store');    // Lưu thêm mới
    Route::get('edit/{id}', [PromotionController::class, 'edit'])->name('edit');   // Form sửa
    Route::post('update/{id}', [PromotionController::class, 'update'])->name('update'); // Lưu sửa
    Route::delete('destroy/{id}', [PromotionController::class, 'destroy'])->name('destroy'); // Xóa
});

/* Order Management Routes */
Route::middleware('auth:web')->prefix('orders')->name('orders.')->group(function () {
    Route::get('index', [OrderBEController::class, 'index'])->name('index');
    Route::get('orders/{id}', [OrderBEController::class, 'show'])->name('show');
});


//FRONTEND
Route::get('/', [HomeController::class, 'index'])->name('home.index');
Route::get('/shop', [ShopGridController::class, 'index'])->name('shop.index');
Route::get('/shop/detailProduct/{id}', [ShopDetailController::class, 'index'])->name('shop.details');
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{category_canonical}/{post_canonical}', [BlogDetailController::class, 'index'])->name('blog.details');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/update', [CartController::class, 'updateItem'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'removeItem'])->name('cart.remove');
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/vnpay_payment', [CheckoutController::class, 'vnpayPayment'])->name('checkout.vnpay_payment');
Route::get('/order/success/{order}', [CheckoutController::class, 'success'])->name('order.success');
Route::get('/order/failed/{order?}', [CheckoutController::class, 'failed'])->name('order.failed');
Route::get('/vnpay/return', [CheckoutController::class, 'vnpayReturn'])->name('vnpay.return');
Route::get('/order', [OrderController::class, 'index'])->name('order.index');
Route::post('/order/{order}/cancel', [OrderController::class, 'cancel'])->name('order.cancelled');
Route::post('/order/{orderId}/reorder', [OrderController::class, 'reorder'])
    ->name('order.reorder');

Route::get('/customer/login', [CustomerController::class, 'index'])->name('customer.index');
Route::post('/customer/postLogin', [CustomerController::class, 'postLogin'])->name('customer.postLogin');
Route::get('/customer/register', [CustomerController::class, 'register'])->name('customer.register');
Route::post('/customer/postRegister', [CustomerController::class, 'store'])->name('customer.postRegister');
Route::post('/customer/postLogout', [CustomerController::class, 'logout'])->name('customer.postLogout');
Route::middleware('auth:customer')->group(function () {
    Route::get('/profile', [UpdateProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile', [UpdateProfileController::class, 'update'])->name('profile.update');
});
//rating
Route::post('/product/{product}/rating', [ShopDetailController::class, 'submitRating'])
    ->middleware('auth:customer')->name('product.submitRating');