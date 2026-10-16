@extends('frontend.index')

@section('content')
    <!-- Hero Section Begin -->
    <section class="hero">
        <div class="container">
            <div class="row">
                <div class="col-lg-3">
                    <div class="hero__categories">
                        <div class="hero__categories__all">
                            <i class="fa fa-bars"></i>
                            <span>Tất cả danh mục</span>
                        </div>
                        <ul>
                            @foreach ($productCategories as $category)
                                <li>
                                    <a href="#">
                                        {{ $category->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="col-lg-9">
                    <div class="hero__search">
                        <div class="hero__search__form">
                            <form action="#">
                                <div class="hero__search__categories">
                                    Tất cả danh mục
                                    <span class="arrow_carrot-down"></span>
                                </div>
                                <input type="text" placeholder="Bạn cần gì?">
                                <button type="submit" class="site-btn">TÌM KIẾM</button>
                            </form>
                        </div>
                        <div class="hero__search__phone">
                            <div class="hero__search__phone__icon">
                                <i class="fa fa-phone"></i>
                            </div>
                            <div class="hero__search__phone__text">
                                <h5>+84 379.66.7431</h5>
                                <span>Hỗ trợ 24/7</span>
                            </div>
                        </div>
                    </div>
                    <div class="hero__item set-bg" data-setbg="frontend2/img/hero/banner.jpg">
                        <div class="hero__text">
                            <span>Công nghệ mới</span>
                            <h2>Máy tính & Điện thoại <br />Chính hãng 100%</h2>
                            <p>Miễn phí giao hàng toàn quốc - Bảo hành chính hãng</p>
                            <a href="{{ route('shop.index') }}" class="primary-btn">Mua sắm ngay</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Hero Section End -->

    <!-- Categories Section Begin -->
    <section class="categories">
        <div class="container">
            <div class="row">
                <div class="categories__slider owl-carousel">
                    @foreach ($productCategories as $category)
                        <div class="col-lg-3">
                            <div class="categories__item set-bg" data-setbg="{{ asset($category->image) }}">
                                <h5><a href="#">{{ $category->name }}</a></h5>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    <!-- Categories Section End -->


    <!-- Featured Section Begin -->
    <section class="featured spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title">
                        <h2>Sản phẩm nổi bật</h2>
                    </div>
                    <div class="featured__controls">
                        <ul>
                            <li class="active" data-filter="*">Tất cả</li>
                            @foreach ($productCategories as $cat)
                                <li data-filter=".cat-{{ $cat->id }}">{{ $cat->name }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="row featured__filter">
                @foreach ($products as $product)
                    @php $priceInfo = $product->getPriceInfo(); @endphp

                    @if (!$priceInfo['hasDiscount'])
                        {{-- Sản phẩm bình thường --}}
                        <div class="col-lg-3 col-md-4 col-sm-6 mix cat-{{ $product->category_id }}">
                            <div class="featured__item">
                                <div class="featured__item__pic set-bg" data-setbg="{{ asset($product->image) }}">
                                    <ul class="featured__item__pic__hover">
                                        <li><a href="#"><i class="fa fa-heart"></i></a></li>
                                        <li><a href="#"><i class="fa fa-retweet"></i></a></li>
                                        <li><a href="#"><i class="fa fa-shopping-cart"></i></a></li>
                                    </ul>
                                </div>
                                <div class="featured__item__text">
                                    <span><a href="#">{{ $product->category_name }}</a></span>
                                    <h6>
                                        <a href="{{ route('shop.details', ['id' => $product->id]) }}">
                                            {{ $product->product_name }}
                                        </a>
                                    </h6>
                                    <h5>{{ number_format($product->base_price, 0, ',', '.') }} đ</h5>
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- Sản phẩm có khuyến mãi --}}
                        <div class="col-lg-3 col-md-4 col-sm-6 mix cat-{{ $product->category_id }}">
                            <div class="product__discount__item">
                                <div class="product__discount__item__pic set-bg" data-setbg="{{ asset($product->image) }}">
                                    <div class="product__discount__percent">
                                        -{{ round($priceInfo['discountPercent'], 1) }}%
                                    </div>
                                    <ul class="product__item__pic__hover">
                                        <li><a href="#"><i class="fa fa-heart"></i></a></li>
                                        <li><a href="#"><i class="fa fa-retweet"></i></a></li>
                                        <li><a href="#"><i class="fa fa-shopping-cart"></i></a></li>
                                    </ul>
                                </div>
                                <div class="product__discount__item__text">
                                    <span><a href="#">{{ $product->category_name }}</a></span>
                                    <h6>
                                        <a href="{{ route('shop.details', ['id' => $product->id]) }}">
                                            {{ $product->product_name }}
                                        </a>
                                    </h6>
                                    <div class="product__item__price">
                                        {{ number_format($priceInfo['finalPrice'], 0, ',', '.') }} đ
                                        <span>{{ number_format($product->base_price, 0, ',', '.') }} đ</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

        </div>
    </section>
    <!-- Featured Section End -->


    <!-- HOT DEAL SECTION -->
    <div id="hot-deal" class="section">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="hot-deal">
                        <ul class="hot-deal-countdown">
                            <li>
                                <div>
                                    <h3>02</h3>
                                    <span>ngày</span>
                                </div>
                            </li>
                            <li>
                                <div>
                                    <h3>10</h3>
                                    <span>giờ</span>
                                </div>
                            </li>
                            <li>
                                <div>
                                    <h3>34</h3>
                                    <span>phút</span>
                                </div>
                            </li>
                            <li>
                                <div>
                                    <h3>60</h3>
                                    <span>giây</span>
                                </div>
                            </li>
                        </ul>
                        <h2 class="text-uppercase">Deal hot trong tuần</h2>
                        <p>Giảm giá lên đến 20% cho tổng đơn hàng</p>
                        <a class="primary-btn cta-btn" href="{{ route('shop.index') }}">Mua sắm ngay</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /HOT DEAL SECTION -->

    <!-- Latest Product Section Begin -->
    <section class="latest-product spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="latest-product__text">
                        <h4>Sản phẩm mới nhất</h4>
                        <div class="latest-product__slider owl-carousel">
                            <div class="latest-prdouct__slider__item">
                                <a href="#" class="latest-product__item">
                                    <div class="latest-product__item__pic">
                                        <img src="#" alt="">
                                    </div>
                                    <div class="latest-product__item__text">
                                        <h6>Sản phẩm mới 1</h6>
                                        <span>100.000đ</span>
                                    </div>
                                </a>
                                <a href="#" class="latest-product__item">
                                    <div class="latest-product__item__pic">
                                        <img src="#" alt="">
                                    </div>
                                    <div class="latest-product__item__text">
                                        <h6>Sản phẩm mới 2</h6>
                                        <span>200.000đ</span>
                                    </div>
                                </a>
                                <a href="#" class="latest-product__item">
                                    <div class="latest-product__item__pic">
                                        <img src="#" alt="">
                                    </div>
                                    <div class="latest-product__item__text">
                                        <h6>Sản phẩm mới 3</h6>
                                        <span>300.000đ</span>
                                    </div>
                                </a>
                            </div>
                            <div class="latest-prdouct__slider__item">
                                <a href="#" class="latest-product__item">
                                    <div class="latest-product__item__pic">
                                        <img src="#" alt="">
                                    </div>
                                    <div class="latest-product__item__text">
                                        <h6>Sản phẩm mới 4</h6>
                                        <span>400.000đ</span>
                                    </div>
                                </a>
                                <a href="#" class="latest-product__item">
                                    <div class="latest-product__item__pic">
                                        <img src="#" alt="">
                                    </div>
                                    <div class="latest-product__item__text">
                                        <h6>Sản phẩm mới 5</h6>
                                        <span>500.000đ</span>
                                    </div>
                                </a>
                                <a href="#" class="latest-product__item">
                                    <div class="latest-product__item__pic">
                                        <img src="#" alt="">
                                    </div>
                                    <div class="latest-product__item__text">
                                        <h6>Sản phẩm mới 6</h6>
                                        <span>600.000đ</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="latest-product__text">
                        <h4>Top sản phẩm</h4>
                        <div class="latest-product__slider owl-carousel">
                            <div class="latest-prdouct__slider__item">
                                <a href="#" class="latest-product__item">
                                    <div class="latest-product__item__pic">
                                        <img src="#" alt="">
                                    </div>
                                    <div class="latest-product__item__text">
                                        <h6>Top 1</h6>
                                        <span>100.000đ</span>
                                    </div>
                                </a>
                                <a href="#" class="latest-product__item">
                                    <div class="latest-product__item__pic">
                                        <img src="#" alt="">
                                    </div>
                                    <div class="latest-product__item__text">
                                        <h6>Top 2</h6>
                                        <span>200.000đ</span>
                                    </div>
                                </a>
                                <a href="#" class="latest-product__item">
                                    <div class="latest-product__item__pic">
                                        <img src="#" alt="">
                                    </div>
                                    <div class="latest-product__item__text">
                                        <h6>Top 3</h6>
                                        <span>300.000đ</span>
                                    </div>
                                </a>
                            </div>
                            <div class="latest-prdouct__slider__item">
                                <a href="#" class="latest-product__item">
                                    <div class="latest-product__item__pic">
                                        <img src="#" alt="">
                                    </div>
                                    <div class="latest-product__item__text">
                                        <h6>Top 4</h6>
                                        <span>400.000đ</span>
                                    </div>
                                </a>
                                <a href="#" class="latest-product__item">
                                    <div class="latest-product__item__pic">
                                        <img src="#" alt="">
                                    </div>
                                    <div class="latest-product__item__text">
                                        <h6>Top 5</h6>
                                        <span>500.000đ</span>
                                    </div>
                                </a>
                                <a href="#" class="latest-product__item">
                                    <div class="latest-product__item__pic">
                                        <img src="#" alt="">
                                    </div>
                                    <div class="latest-product__item__text">
                                        <h6>Top 6</h6>
                                        <span>600.000đ</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="latest-product__text">
                        <h4>Sản phẩm đánh giá cao</h4>
                        <div class="latest-product__slider owl-carousel">
                            <div class="latest-prdouct__slider__item">
                                <a href="#" class="latest-product__item">
                                    <div class="latest-product__item__pic">
                                        <img src="#" alt="">
                                    </div>
                                    <div class="latest-product__item__text">
                                        <h6>Đánh giá cao 1</h6>
                                        <span>100.000đ</span>
                                    </div>
                                </a>
                                <a href="#" class="latest-product__item">
                                    <div class="latest-product__item__pic">
                                        <img src="#" alt="">
                                    </div>
                                    <div class="latest-product__item__text">
                                        <h6>Đánh giá cao 2</h6>
                                        <span>200.000đ</span>
                                    </div>
                                </a>
                                <a href="#" class="latest-product__item">
                                    <div class="latest-product__item__pic">
                                        <img src="#" alt="">
                                    </div>
                                    <div class="latest-product__item__text">
                                        <h6>Đánh giá cao 3</h6>
                                        <span>300.000đ</span>
                                    </div>
                                </a>
                            </div>
                            <div class="latest-prdouct__slider__item">
                                <a href="#" class="latest-product__item">
                                    <div class="latest-product__item__pic">
                                        <img src="#" alt="">
                                    </div>
                                    <div class="latest-product__item__text">
                                        <h6>Đánh giá cao 4</h6>
                                        <span>400.000đ</span>
                                    </div>
                                </a>
                                <a href="#" class="latest-product__item">
                                    <div class="latest-product__item__pic">
                                        <img src="#" alt="">
                                    </div>
                                    <div class="latest-product__item__text">
                                        <h6>Đánh giá cao 5</h6>
                                        <span>500.000đ</span>
                                    </div>
                                </a>
                                <a href="#" class="latest-product__item">
                                    <div class="latest-product__item__pic">
                                        <img src="#" alt="">
                                    </div>
                                    <div class="latest-product__item__text">
                                        <h6>Đánh giá cao 3</h6>
                                        <span>300.000đ</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Latest Product Section End -->

    <!-- Blog Section Begin -->
    <section class="from-blog spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title from-blog__title">
                        <h2>Tin tức</h2>
                    </div>
                </div>
            </div>
            <div class="row">
                @foreach ($posts as $post)
                    <div class="col-lg-4 col-md-4 col-sm-6">
                        <div class="blog__item">
                            <div class="blog__item__pic">
                                <img src="{{ asset($post->image) }}" alt="">
                            </div>
                            <div class="blog__item__text">
                                <ul>
                                    <li><i class="fa fa-calendar-o"></i>
                                        {{ $post->created_at->locale('vi')->translatedFormat('j F, Y') }}
                                    </li>
                                    <li><i class="fa fa-comment-o"></i> 5</li>
                                </ul>
                                <h5><a
                                        href="{{ route('blog.details', [
                                            'category_canonical' => $post->category_canonical,
                                            'post_canonical' => $post->post_canonical,
                                        ]) }}">
                                        {{ $post->post_name }}
                                    </a></h5>
                                <p>{{ $post->post_description }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    <!-- Blog Section End -->
@endsection
