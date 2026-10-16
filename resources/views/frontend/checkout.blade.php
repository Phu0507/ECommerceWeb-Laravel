@extends('frontend.index')

@section('content')
    @include('frontend.component.search')
    <!-- Breadcrumb Section Begin -->
    <section class="breadcrumb-section set-bg" data-setbg="{{ asset('frontend2/img/breadcrumbs-bg.jpg') }}">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="breadcrumb__text">
                        <h2>Thanh toán</h2>
                        <div class="breadcrumb__option">
                            <a href="./index.html">Trang chủ</a>
                            <span>Thanh toán</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Breadcrumb Section End -->

    <!-- Checkout Section Begin -->
    <section class="checkout spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h6><span class="icon_tag_alt"></span> Có mã giảm giá? <a href="{{ route('cart.index') }}">Nhấp vào
                            đây</a> để nhập mã của
                        bạn
                    </h6>
                </div>
            </div>
            <div class="checkout__form">
                <h4>Thông tin thanh toán</h4>
                <form action="{{ url('/vnpay_payment') }}" method="POST">
                    @csrf
                    @php
                        $customer = Auth::guard('customer')->user();
                    @endphp
                    <div class="row">
                        <div class="col-lg-8 col-md-6">
                            <div class="checkout__input">
                                <p>Họ tên<span>*</span></p>
                                <input type="text" name="name" value="{{ $customer ? $customer->name : '' }}"
                                    {{ $customer ? 'readonly' : '' }}>
                            </div>
                            <div class="checkout__input">
                                <p>Địa chỉ<span>*</span></p>
                                <input type="text" name="address" value="{{ $customer ? $customer->address : '' }}"
                                    placeholder="Số nhà, đường, đơn vị, v.v. (tùy chọn)">
                            </div>
                            <div class="checkout__input">
                                <p>Phường/Xã<span>*</span></p>
                                <input type="text" name="ward" value="{{ $customer ? $customer->ward_id : '' }}">
                            </div>
                            <div class="checkout__input">
                                <p>Quận/Huyện<span>*</span></p>
                                <input type="text" name="district" value="{{ $customer ? $customer->district_id : '' }}">
                            </div>
                            <div class="checkout__input">
                                <p>Thành phố/Tỉnh<span>*</span></p>
                                <input type="text" name="province" value="{{ $customer ? $customer->province_id : '' }}">
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="checkout__input">
                                        <p>Số điện thoại<span>*</span></p>
                                        <input type="text" name="phone"
                                            value="{{ $customer ? $customer->phone : '' }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="checkout__input">
                                        <p>Email</p>
                                        <input type="text" name="email"
                                            value="{{ $customer ? $customer->email : '' }}"
                                            {{ $customer ? 'readonly' : '' }}>
                                    </div>
                                </div>
                            </div>
                            <div class="checkout__input">
                                <p>Ghi chú đơn hàng</p>
                                <input type="text" name="note"
                                    placeholder="Ghi chú về đơn hàng của bạn, ví dụ: ghi chú đặc biệt cho việc giao hàng.">
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="checkout__order">
                                <h4>Đơn hàng của bạn</h4>
                                <div class="checkout__order__products">Sản phẩm <span>Tổng</span></div>
                                <ul>
                                    @foreach ($cart->items as $item)
                                        <li>
                                            <div class="product-info">
                                                <div class="product-name">{{ $item->product->translation->name }}</div>
                                                <div class="variant-name">{{ $item->variant->translation->name }}</div>
                                            </div>
                                            <span class="product-price">
                                                {{ number_format($item->final_price * $item->quantity, 0, ',', '.') }}₫
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                                <div class="checkout__order__subtotal">Tổng tiền hàng
                                    <span>{{ number_format($cart->subtotal, 0, ',', '.') }}₫</span>
                                </div>
                                <div class="checkout__order__discount">Giảm giá <span>-
                                        {{ number_format($cart->promotion_discount, 0, ',', '.') }}₫</span></div>
                                <div class="checkout__order__total">Tổng cộng phải trả
                                    <span>{{ number_format($cart->total, 0, ',', '.') }}₫</span>
                                </div>
                                <div class="checkout__input__checkbox">
                                    <label for="payment_cod">
                                        Thanh toán khi nhận hàng
                                        <input type="checkbox" id="payment_cod" name="payment_method" value="cod">
                                        <span class="checkmark"></span>
                                    </label>
                                </div>
                                <div class="checkout__input__checkbox">
                                    <label for="payment_vnpay">
                                        Thanh toán qua VnPay
                                        <input type="checkbox" id="payment_vnpay" name="payment_method" value="vnpay">
                                        <span class="checkmark"></span>
                                    </label>
                                </div>
                                <input type="hidden" name="amount" value="{{ $cart->total }}">
                                <button name="redirect" type="submit" class="site-btn" id="btnCheckout" disabled>ĐẶT
                                    HÀNG</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <!-- Checkout Section End -->
@endsection
