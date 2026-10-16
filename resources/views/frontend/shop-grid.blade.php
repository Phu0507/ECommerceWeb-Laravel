@extends('frontend.index')

@section('content')
    @include('frontend.component.search')
    <!-- Breadcrumb Section Begin -->
    <section class="breadcrumb-section set-bg" data-setbg="frontend2/img/breadcrumbs-bg.jpg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="breadcrumb__text">
                        <h2>Organi Shop</h2>
                        <div class="breadcrumb__option">
                            <a href="{{ route('home.index') }}">Trang chủ</a>
                            <span>Cửa hàng</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Breadcrumb Section End -->

    <!-- Product Section Begin -->
    <form id="filterSortForm" method="GET" action="{{ route('shop.index') }}">
        <section class="product spad">
            <div class="container">
                <div class="row">
                    <div class="col-lg-3 col-md-5">
                        <div class="sidebar">
                            <div class="sidebar__item">
                                <h4>Danh mục</h4>
                                @foreach ($productCategories as $category)
                                    <div>
                                        <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                                            id="cat{{ $category->id }}"
                                            onchange="document.getElementById('filterSortForm').submit();"
                                            {{ in_array($category->id, request()->get('categories', [])) ? 'checked' : '' }}>
                                        <label for="cat{{ $category->id }}">{{ $category->name }}</label>
                                    </div>
                                @endforeach
                            </div>
                            <div class="sidebar__item">
                                <h4>Giá</h4>
                                <div class="price-range-wrap">
                                    <div class="price-range ui-slider ui-corner-all ui-slider-horizontal ui-widget ui-widget-content"
                                        data-min="1" data-max="100000000">
                                        <div class="ui-slider-range ui-corner-all ui-widget-header"></div>
                                        <span tabindex="0" class="ui-slider-handle ui-corner-all ui-state-default"></span>
                                        <span tabindex="0" class="ui-slider-handle ui-corner-all ui-state-default"></span>
                                    </div>
                                    <div class="range-slider">
                                        <div class="price-input">
                                            <input type="text" id="minamount" name="min_price"
                                                value="{{ request('min_price', 1) }}">
                                            <input type="text" id="maxamount" name="max_price"
                                                value="{{ request('max_price', 100000000) }}">
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <button type="submit" class="btn btn-success btn-block">Áp dụng</button>
                            </div>
                            <div class="sidebar__item">
                                <div class="latest-product__text">
                                    <h4>Liên quan</h4>
                                    <div class="latest-product__slider owl-carousel">
                                        <div class="latest-prdouct__slider__item">
                                            <a href="#" class="latest-product__item">
                                                <div class="latest-product__item__pic">
                                                    <img src="" alt="">
                                                </div>
                                                <div class="latest-product__item__text">
                                                    <h6>Sản phẩm 1</h6>
                                                    <span>100.000đ</span>
                                                </div>
                                            </a>
                                            <a href="#" class="latest-product__item">
                                                <div class="latest-product__item__pic">
                                                    <img src="" alt="">
                                                </div>
                                                <div class="latest-product__item__text">
                                                    <h6>Sản phẩm 2</h6>
                                                    <span>200.000đ</span>
                                                </div>
                                            </a>
                                            <a href="#" class="latest-product__item">
                                                <div class="latest-product__item__pic">
                                                    <img src="" alt="">
                                                </div>
                                                <div class="latest-product__item__text">
                                                    <h6>Sản phẩm 3</h6>
                                                    <span>300.000đ</span>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="latest-prdouct__slider__item">
                                            <a href="#" class="latest-product__item">
                                                <div class="latest-product__item__pic">
                                                    <img src="" alt="">
                                                </div>
                                                <div class="latest-product__item__text">
                                                    <h6>Sản phẩm 4</h6>
                                                    <span>400.000đ</span>
                                                </div>
                                            </a>
                                            <a href="#" class="latest-product__item">
                                                <div class="latest-product__item__pic">
                                                    <img src="" alt="">
                                                </div>
                                                <div class="latest-product__item__text">
                                                    <h6>Sản phẩm 5</h6>
                                                    <span>500.000đ</span>
                                                </div>
                                            </a>
                                            <a href="#" class="latest-product__item">
                                                <div class="latest-product__item__pic">
                                                    <img src="" alt="">
                                                </div>
                                                <div class="latest-product__item__text">
                                                    <h6>Sản phẩm 6</h6>
                                                    <span>600.000đ</span>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-9 col-md-7">
                        <div class="filter__item">
                            <div class="row">
                                <div class="col-lg-4 col-md-5">
                                    <div class="filter__sort">
                                        <span>Sắp xếp</span>
                                        <select name="sort"
                                            onchange="document.getElementById('filterSortForm').submit()">
                                            <option value="" {{ request('sort') == '' ? 'selected' : '' }}>Mặc định
                                            </option>
                                            <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Giá từ
                                                thấp đến cao</option>
                                            <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>Giá
                                                từ
                                                cao đến thấp</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4">
                                    <div class="filter__found">
                                        <h6><span>{{ $products->total() }}</span> sản phẩm được tìm thấy</h6>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-3">
                                    <div class="filter__option">
                                        <span class="icon_grid-2x2"></span>
                                        <span class="icon_ul"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            @foreach ($products as $product)
                                @php $priceInfo = $product->getPriceInfo(); @endphp
                                @if (!$priceInfo['hasDiscount'])
                                    {{-- Sản phẩm bình thường --}}
                                    <div class="col-lg-4 col-md-6 col-sm-6">
                                        <div class="product__item">
                                            <div class="product__item__pic set-bg"
                                                data-setbg="{{ asset($product->image) }}">
                                                <ul class="product__item__pic__hover">
                                                    <li><a href="#"><i class="fa fa-heart"></i></a></li>
                                                    <li><a href="#"><i class="fa fa-retweet"></i></a></li>
                                                    <li><a href="#"><i class="fa fa-shopping-cart"></i></a></li>
                                                </ul>
                                            </div>
                                            <div class="product__item__text">
                                                <span><a href="#">{{ $product->category_name }}</a></span>
                                                <h6> <a href="{{ route('shop.details', ['id' => $product->id]) }}">
                                                        {{ $product->product_name }}</a></h6>
                                                <h5>{{ number_format($product->base_price, 0, ',', '.') }} đ</h5>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    {{-- Sản phẩm có khuyến mãi --}}
                                    <div class="col-lg-4">
                                        <div class="product__discount__item">
                                            <div class="product__discount__item__pic set-bg"
                                                data-setbg="{{ asset($product->image) }}">
                                                <div class="product__discount__percent">
                                                    -{{ round($priceInfo['discountPercent'], 1) }}%</div>
                                                <ul class="product__item__pic__hover">
                                                    <li><a href="#"><i class="fa fa-heart"></i></a></li>
                                                    <li><a href="#"><i class="fa fa-retweet"></i></a></li>
                                                    <li><a href="#"><i class="fa fa-shopping-cart"></i></a></li>
                                                </ul>
                                            </div>
                                            <div class="product__discount__item__text">
                                                <span><a href="#">{{ $product->category_name }}</a></span>
                                                <h6><a href="{{ route('shop.details', ['id' => $product->id]) }}">
                                                        {{ $product->product_name }}</a></h6>
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
                        {{ $products->links('frontend.component.paginationCustom') }}
                    </div>
                </div>
            </div>
        </section>
    </form>
    <!-- Product Section End -->
@endsection
