@extends('frontend.index')
@section('content')
    @include('frontend.component.search')
    <!-- Breadcrumb Section Begin -->
    <section class="breadcrumb-section set-bg" data-setbg="{{ asset('frontend2/img/breadcrumbs-bg.jpg') }}">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="breadcrumb__text">
                        <h2>{{ $product['name'] }}</h2>
                        <div class="breadcrumb__option">
                            <a href="{{ route('home.index') }}">Trang chủ</a>
                            <a href="{{ route('shop.index') }}">Cửa hàng</a>
                            <span>{{ $product['name'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Breadcrumb Section End -->

    <!-- Product Details Section Begin -->
    <section class="product-details spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <div class="product__details__pic">
                        <div class="product__details__pic__item">
                            <img class="product__details__pic__item--large" src="{{ asset($product['image']) }}"
                                alt="{{ $product['name'] }}">
                        </div>

                        <div class="product__details__pic__slider owl-carousel">
                            @if (!empty($product['variant_image']))
                                @foreach ($product['variant_image'] as $variantImgs)
                                    @foreach ($variantImgs as $img)
                                        <img data-imgbigurl="{{ $img }}" src="{{ $img }}" alt="">
                                    @endforeach
                                @endforeach
                            @else
                                {{-- nếu không có biến thể thì hiển thị ảnh chính --}}
                                <img data-imgbigurl="{{ asset($product['image']) }}" src="{{ asset($product['image']) }}"
                                    alt="">
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 col-md-6">
                    <div class="product__details__text">
                        <h3>{{ $product['name'] }}</h3>
                        <div class="product__details__rating">
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star-half-o"></i>
                            <span>(18 đánh giá)</span>
                        </div>
                        {{-- Giá sản phẩm --}}
                        <div id="product-price" class="product__details__price-box">
                            @php
                                $basePrice = (float) $product['base_price'];
                                $hasPromotion = $product['promotions']->isNotEmpty();
                                $finalPrice = $basePrice;

                                if ($hasPromotion) {
                                    $promotion = $product['promotions']->first();
                                    if ($promotion['discount_type'] === 'percentage') {
                                        $finalPrice =
                                            $basePrice * (1 - $promotion['discounts'][0]['discount_value'] / 100);
                                    } elseif ($promotion['discount_type'] === 'fixed') {
                                        $finalPrice = $basePrice - $promotion['discounts'][0]['discount_value'];
                                    }
                                }
                            @endphp

                            @if ($hasPromotion)
                                <div class="product__details__price">{{ number_format($finalPrice, 0, ',', '.') }}₫</div>
                                <span
                                    class="product__details__oldprice">{{ number_format($basePrice, 0, ',', '.') }}₫</span>
                            @else
                                <div class="product__details__price">{{ number_format($basePrice, 0, ',', '.') }}₫</div>
                            @endif
                        </div>
                        @php
                            // Gom các option theo category_id và dedupe theo value_id
                            $optionsByCategory = [];
                            foreach ($product['variant_attributes'] as $row) {
                                foreach ($row['category_id'] as $i => $catId) {
                                    $valId = $row['value_id'][$i];
                                    $label = trim($row['name'][$i]);
                                    $optionsByCategory[$catId][$valId] = $label;
                                }
                            }
                        @endphp

                        @if (!empty($optionsByCategory))
                            <div class="product__details__option">
                                @php $groupIndex = 1; @endphp
                                @foreach ($optionsByCategory as $catId => $options)
                                    <div class="product__details__option__size" data-category="{{ $catId }}">
                                        @foreach ($options as $valueId => $label)
                                            <input type="radio" name="value{{ $groupIndex }}" id="{{ $valueId }}"
                                                value="{{ $valueId }}">
                                            <label for="{{ $valueId }}">{{ $label }}</label>
                                        @endforeach
                                    </div>
                                    @php $groupIndex++; @endphp
                                @endforeach
                            </div>
                        @endif

                        <div class="product__details__quantity">
                            <div class="quantity">
                                <div class="pro-qty">
                                    <input type="text" value="1">
                                </div>
                            </div>
                        </div>
                        <a href="javascript:void(0)" class="primary-btn add-to-cart"
                            data-product-id="{{ $product['id'] }}">
                            Thêm vào giỏ hàng
                        </a>
                        <a href="#" class="heart-icon"><span class="icon_heart_alt"></span></a>
                        <ul>
                            <li><b>Tình trạng</b> <span>Còn hàng</span></li>
                            <li><b>Vận chuyển</b> <span>Giao hàng nhanh. <samp>Nhận hàng miễn phí hôm nay</samp></span>
                            </li>
                            <li><b>Cân nặng</b> <span>0.5 kg</span></li>
                            <li><b>Chia sẻ trên</b>
                                <div class="share">
                                    <a href="#"><i class="fa fa-facebook"></i></a>
                                    <a href="#"><i class="fa fa-twitter"></i></a>
                                    <a href="#"><i class="fa fa-instagram"></i></a>
                                    <a href="#"><i class="fa fa-pinterest"></i></a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="product__details__tab">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#tabs-1" role="tab"
                                    aria-selected="true">Mô tả</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tabs-2" role="tab"
                                    aria-selected="false">Thông tin</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tabs-3" role="tab"
                                    aria-selected="false">Đánh giá <span>(1)</span></a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="tabs-1" role="tabpanel">
                                <div class="product__details__tab__desc">
                                    <h6>Mô tả sản phẩm</h6>
                                    {!! $product['description'] !!}
                                </div>
                            </div>
                            <div class="tab-pane" id="tabs-2" role="tabpanel">
                                <div class="product__details__tab__desc">
                                    <h6>Thông tin sản phẩm</h6>
                                </div>
                            </div>
                            <div class="tab-pane" id="tabs-3" role="tabpanel">
                                <div class="product__details__tab__desc">
                                    <div class="row">
                                        <!-- Rating -->
                                        <div class="col-md-3">
                                            @include('frontend.component.rating_summary', [
                                                'avgRating' => $avgRating,
                                                'starsCount' => $starsCount,
                                            ])
                                        </div>

                                        <!-- /Rating -->

                                        <!-- Reviews -->
                                        <div class="col-md-6">
                                            <div id="reviews">
                                                <ul class="reviews">
                                                    @foreach ($ratings as $rating)
                                                        @include('frontend.component.single_review', [
                                                            'rating' => $rating,
                                                        ])
                                                    @endforeach
                                                </ul>
                                                {{ $ratings->links('frontend.component.paginationCustom') }}
                                            </div>
                                        </div>
                                        <!-- /Reviews -->

                                        <!-- Review Form -->
                                        <div class="col-md-3">
                                            <div id="review-form">
                                                <form id="product-review-form" class="review-form"
                                                    data-product-id="{{ $product['id'] }}"
                                                    data-csrf="{{ csrf_token() }}">
                                                    <textarea class="input" name="comment" placeholder="Đánh giá của bạn"></textarea>
                                                    <div class="input-rating">
                                                        <span>Đánh giá sao: </span>
                                                        <div class="stars">
                                                            <input id="star5" name="rating" value="5"
                                                                type="radio"><label for="star5"></label>
                                                            <input id="star4" name="rating" value="4"
                                                                type="radio"><label for="star4"></label>
                                                            <input id="star3" name="rating" value="3"
                                                                type="radio"><label for="star3"></label>
                                                            <input id="star2" name="rating" value="2"
                                                                type="radio"><label for="star2"></label>
                                                            <input checked id="star1" name="rating" value="1"
                                                                type="radio"><label for="star1"></label>
                                                        </div>
                                                    </div>
                                                    <button type="submit" class="primary-btn">Đánh giá</button>
                                                </form>
                                            </div>
                                        </div>
                                        <!-- /Review Form -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Product Details Section End -->

    <!-- Related Product Section Begin -->
    <section class="related-product">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title related__product__title">
                        <h2>Sản phẩm liên quan</h2>
                    </div>
                </div>
            </div>
            <div class="row">
                @foreach ($product['relatedProducts'] as $related)
                    @php $priceInfo = $related->getPriceInfo(); @endphp
                    @if (!$priceInfo['hasDiscount'])
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="product__item">
                                <div class="product__item__pic set-bg" data-setbg="{{ asset($related->image) }}">
                                    <ul class="product__item__pic__hover">
                                        <li><a href="#"><i class="fa fa-heart"></i></a></li>
                                        <li><a href="#"><i class="fa fa-retweet"></i></a></li>
                                        <li><a href="#"><i class="fa fa-shopping-cart"></i></a></li>
                                    </ul>
                                </div>
                                <div class="product__item__text">
                                    <h6><a href="{{ route('shop.details', ['id' => $related->id]) }}">
                                            {{ $related->product_language->first()->name ?? '' }}</a></h6>
                                    <h5>{{ number_format($related->base_price, 0, ',', '.') }} đ</h5>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="product__discount__item">
                                <div class="product__discount__item__pic set-bg"
                                    data-setbg="{{ asset($related->image) }}">
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
                                    <h6><a href="{{ route('shop.details', ['id' => $related->id]) }}">
                                            {{ $related->product_language->first()->name ?? '' }}</a></h6>
                                    <div class="product__item__price">
                                        {{ number_format($priceInfo['finalPrice'], 0, ',', '.') }} đ
                                        <span>{{ number_format($related->base_price, 0, ',', '.') }} đ</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </section>
    <script>
        window.productVariants = @json($product['variant_attributes']);
        window.variantPrices = @json($product['variant_price']);
        window.variantSku = @json($product['variant_sku']);
        window.promotions = @json($product['promotions']);

        window.Laravel = {
            csrfToken: "{{ csrf_token() }}",
            addToCartUrl: "{{ route('cart.add') }}",
        };
    </script>
    <!-- Related Product Section End -->
@endsection
