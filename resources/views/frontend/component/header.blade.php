<!-- Page Preloder -->
<div id="preloder">
    <div class="loader"></div>
</div>

<!-- Header Section Begin -->
<header class="header">
    <div class="header__top">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <div class="header__top__left">
                        <ul>
                            <li><i class="fa fa-envelope"></i> ogani@gmail.com</li>
                            <li>Miễn phí vận chuyển</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="header__top__right">
                        <div class="header__top__right__social">
                            <a href="#"><i class="fa fa-facebook"></i></a>
                            <a href="#"><i class="fa fa-twitter"></i></a>
                            <a href="#"><i class="fa fa-linkedin"></i></a>
                            <a href="#"><i class="fa fa-pinterest-p"></i></a>
                        </div>
                        {{-- <div class="header__top__right__language">
                            <img src="frontend2/img/language.png" alt="">
                            <div>Việt Nam</div>
                            <span class="arrow_carrot-down"></span>
                            <ul>
                                <li><a href="#">Tiếng Tây Ban Nha</a></li>
                                <li><a href="#">Tiếng Anh</a></li>
                            </ul>
                        </div> --}}
                        <div class="header__top__right__auth">
                            @if (Auth::guard('customer')->check())
                                <div class="dropdown d-inline">
                                    <a href="#" class="dropdown-toggle" id="customerDropdown"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-user"></i> {{ Auth::guard('customer')->user()->name }}
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="customerDropdown">
                                        <a class="dropdown-item" href="{{ route('profile.index') }}">Tài khoản</a>
                                        <a class="dropdown-item" href="{{ route('order.index') }}">Đơn hàng</a>
                                        <form action="{{ route('customer.postLogout') }}" method="POST" class="m-0">
                                            @csrf
                                            <button type="submit" class="dropdown-item">Đăng xuất</button>
                                        </form>
                                    </div>
                                </div>
                            @else
                                <a href="{{ route('customer.index') }}"><i class="fa fa-user"></i> Đăng nhập</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-lg-3">
                <div class="header__logo">
                    <a href="{{ route('home.index') }}"><img src="{{ asset('frontend2/img/logo.png') }}"
                            alt=""></a>
                </div>
            </div>
            <div class="col-lg-6">
                <nav class="header__menu">
                    <ul>
                        <li class="active"><a href="{{ route('home.index') }}">Trang chủ</a></li>
                        <li><a href="{{ route('shop.index') }}">Cửa hàng</a></li>
                        {{-- <li><a href="#">Pages</a>
                            <ul class="header__menu__dropdown">
                                <li><a href="./shoping-cart.html">Giỏ hàng</a></li>
                                <li><a href="./checkout.html">Thanh toán</a></li>
                            </ul>
                        </li> --}}
                        <li><a href="{{ route('blog.index') }}">Tin tức</a></li>
                        <li><a href="#">Liên hệ</a></li>
                    </ul>
                </nav>
            </div>
            <div class="col-lg-3">
                <div class="header__cart">
                    <ul>
                        {{-- <li><a href="#"><i class="fa fa-heart"></i> <span>1</span></a></li> --}}
                        <li><a href="{{ route('cart.index') }}"><i class="fa fa-shopping-bag"></i>
                                <span>{{ $cart->items->count() }}</span></a>
                        </li>
                    </ul>
                    <div class="header__cart__price">Tổng:
                        <span>{{ number_format($cart->total ?? 0, 0, ',', '.') }}₫</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="humberger__open">
            <i class="fa fa-bars"></i>
        </div>
    </div>
</header>
<!-- Header Section End -->
