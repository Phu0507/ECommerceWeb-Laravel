@extends('frontend.index')

@section('content')
    @include('frontend.component.search')
    <!-- Breadcrumb Section Begin -->
    <section class="breadcrumb-section set-bg" data-setbg="{{ asset('frontend2/img/breadcrumbs-bg.jpg') }}">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="breadcrumb__text">
                        <h2>Giỏ hàng</h2>
                        <div class="breadcrumb__option">
                            <a href="./index.html">Trang chủ</a>
                            <span>Giỏ hàng</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Breadcrumb Section End -->

    <!-- Shoping Cart Section Begin -->
    <section class="shoping-cart spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="shoping__cart__table">
                        <table>
                            <thead>
                                <tr>
                                    <th class="shoping__product">Sản phẩm</th>
                                    <th>Giá</th>
                                    <th>Số lượng</th>
                                    <th>Tổng</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cart->items as $item)
                                    <tr data-item-id="{{ $item->id }}">
                                        @php
                                            $album = $item->variant->album ?? '';
                                            $images = $album ? explode(',', $album) : [];
                                            $firstImage = count($images) > 0 ? $images[0] : $item->product->image;
                                        @endphp

                                        <td class="shoping__cart__item">
                                            <img src="{{ asset($firstImage) }}" alt="">
                                            <h5>{{ $item->product->translation->name }}</h5>
                                            <span>{{ $item->variant->translation->name }}</span>
                                        </td>
                                        <td class="shoping__cart__price">
                                            @if ($item->price > $item->final_price)
                                                <span
                                                    class="price-old">{{ number_format($item->price, 0, ',', '.') }}₫</span>
                                            @endif
                                            <div class="price-current">
                                                {{ number_format($item->final_price, 0, ',', '.') }}₫
                                            </div>
                                        </td>
                                        <td class="shoping__cart__quantity">
                                            <div class="quantity">
                                                <div class="pro-qty">
                                                    <input type="text" class="item-quantity"
                                                        value="{{ $item->quantity }}">
                                                </div>
                                            </div>
                                        </td>
                                        <td class="shoping__cart__total">
                                            {{ number_format($item->final_price * $item->quantity, 0, ',', '.') }}₫
                                        </td>
                                        <td class="shoping__cart__item__close">
                                            <span class="icon_close remove-item"></span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="shoping__cart__btns">
                        {{-- <a href="#" class="primary-btn cart-btn">TIẾP TỤC MUA SẮM</a> --}}
                        <a href="{{ route('shop.index') }}" class="primary-btn cart-btn cart-btn-right"><span
                                class="icon_loading"></span>
                            Tiếp tục mua sắm</a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="shoping__continue">
                        <div class="shoping__discount">
                            <h5>Mã giảm giá</h5>
                            <form action="#">
                                <input type="text" placeholder="Nhập mã giảm giá của bạn">
                                <button type="submit" class="site-btn">ÁP DỤNG MÃ GIẢM GIÁ</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="shoping__checkout">
                        <h5>Tổng giỏ hàng</h5>
                        <ul>
                            <li class="subtotal">Tổng tiền hàng
                                <span>{{ number_format($cart->subtotal, 0, ',', '.') }}₫</span>
                            </li>
                            <li class="discount">Giảm giá
                                <span>- {{ number_format($cart->promotion_discount, 0, ',', '.') }}₫</span>
                            </li>
                            <li class="total">Tổng cộng phải trả
                                <span>{{ number_format($cart->total, 0, ',', '.') }}₫</span>
                            </li>
                        </ul>
                        @if ($cart->items->count() > 0)
                            <a href="{{ route('checkout.index') }}" class="primary-btn" id="btn-checkout">THANH TOÁN</a>
                        @else
                            <a href="javascript:void(0)" class="primary-btn"
                                style="background: #ccc; cursor: not-allowed;">THANH TOÁN</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Shoping Cart Section End -->
    <script>
        var isLoggedIn = @json(Auth::guard('customer')->check());

        var btnCheckout = document.getElementById('btn-checkout');
        if (btnCheckout) {
            btnCheckout.addEventListener('click', function(e) {
                e.preventDefault();
                if (!isLoggedIn) {
                    Swal.fire({
                        title: 'Bạn chưa đăng nhập',
                        text: "Bạn có muốn đăng nhập để lưu đơn hàng không? Nếu không, đơn hàng có thể bị mất.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Đăng nhập',
                        cancelButtonText: 'Tiếp tục thanh toán'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "{{ route('customer.index') }}";
                        } else if (result.isDismissed && result.dismiss === Swal.DismissReason.cancel) {
                            window.location.href = "{{ route('checkout.index') }}";
                        }
                    });
                } else {
                    window.location.href = "{{ route('checkout.index') }}";
                }
            });
        }
    </script>
@endsection
