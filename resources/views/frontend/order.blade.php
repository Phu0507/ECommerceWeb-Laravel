@extends('frontend.index')
@section('content')
    @include('frontend.component.search')

    <!-- Breadcrumb Section Begin -->
    <section class="breadcrumb-section set-bg" data-setbg="frontend2/img/breadcrumbs-bg.jpg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="breadcrumb__text">
                        <h2>Đơn hàng của tôi</h2>
                        <div class="breadcrumb__option">
                            <a href="{{ route('home.index') }}">Trang chủ</a>
                            <span>Đơn hàng</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Breadcrumb Section End -->

    <div class="col-lg-12 mt-4">
        <div class="product__details__tab">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#tabs-1" role="tab">Chờ xác nhận</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tabs-2" role="tab">Chờ giao hàng</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tabs-3" role="tab">Đã giao</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tabs-4" role="tab">Đã hủy</a>
                </li>
            </ul>

            <div class="tab-content mt-3">
                <!-- Chờ xác nhận (pending) -->
                <div class="tab-pane active" id="tabs-1" role="tabpanel">
                    @forelse ($orders->where('order_status', 'pending') as $order)
                        <div class="order-card mb-3 p-3 shadow-sm rounded">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="order-header d-flex flex-column">
                                    <div class="mb-1">
                                        <span class="badge badge-warning">Chờ xác nhận</span>
                                        <strong>#{{ $order->id }}</strong>
                                        <span class="text-muted ml-2">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                    <div>
                                        @if ($order->payment_status === 'unpaid')
                                            <span class="badge badge-danger">Thanh toán khi nhận hàng</span>
                                        @elseif($order->payment_status === 'paid')
                                            <span class="badge badge-success">Đã thanh toán</span>
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    <form action="{{ route('order.cancelled', $order->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Hủy đơn hàng</button>
                                    </form>
                                </div>
                            </div>

                            <div class="order-items mb-2">
                                <ul class="list-unstyled mb-0">
                                    @foreach ($order->orderDetails as $item)
                                        @php
                                            $album = $item->variant->album ?? '';
                                            $images = $album ? explode(',', $album) : [];
                                            $firstImage = count($images) > 0 ? $images[0] : $item->product->image;
                                        @endphp
                                        <li class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="product-order-img">
                                                    <img src="{{ asset($firstImage) }}" alt="">
                                                </div>
                                                <div class="product-order-info">
                                                    <div class="product-name">{{ $item->product->translation->name }}</div>
                                                    @if ($item->variant)
                                                        <div class="product-variant text-muted small">
                                                            {{ $item->variant->translation->name }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                @if ($item->discount != 0)
                                                    <div class="product-price">
                                                        {{ number_format($item->price, 0, ',', '.') }}₫
                                                    </div>
                                                @endif
                                                <div class="product-final-price">
                                                    {{ number_format($item->final_price, 0, ',', '.') }}₫
                                                </div>
                                                <div class="product-quantity text-muted small">x{{ $item->quantity }}</div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <div class="order-total text-right font-weight-bold">
                                Tổng số tiền: <span
                                    class="text-primary">{{ number_format($order->total, 0, ',', '.') }}₫</span>
                            </div>
                        </div>
                    @empty
                        @include('frontend.component.order.emptyOrder')
                    @endforelse
                </div>
                <!-- Chờ giao hàng (confirmed/shipping) -->
                <div class="tab-pane" id="tabs-2" role="tabpanel">
                    @forelse ($orders->whereIn('order_status', ['confirmed', 'shipping']) as $order)
                        <div class="order-card mb-3 p-3 shadow-sm rounded">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="order-header d-flex flex-column">
                                    <!-- Dòng trên: trạng thái đơn, mã, ngày -->
                                    <div class="mb-1">
                                        @if ($order->order_status == 'confirmed')
                                            <span class="badge badge-primary">Chờ giao</span>
                                        @elseif($order->order_status == 'shipping')
                                            <span class="badge badge-info">Đang giao</span>
                                        @endif
                                        <strong>#{{ $order->id }}</strong>
                                        <span class="text-muted ml-2">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                    <!-- Dòng dưới: trạng thái thanh toán -->
                                    <div>
                                        @if ($order->payment_status === 'unpaid')
                                            <span class="badge badge-danger">Thanh toán khi nhận hàng</span>
                                        @elseif($order->payment_status === 'paid')
                                            <span class="badge badge-success">Đã thanh toán</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="order-actions text-right mt-2">
                                    @if ($order->order_status == 'confirmed')
                                        <!-- Cho phép hủy khi chờ giao -->
                                        <form action="{{ route('order.cancelled', $order->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                Hủy đơn hàng
                                            </button>
                                        </form>
                                        <a href="#" class="btn btn-sm btn-outline-primary">
                                            Liên hệ người bán
                                        </a>
                                    @elseif ($order->order_status == 'shipping')
                                        <!-- Đang giao: chỉ hiển thị nút liên hệ -->
                                        <a href="#" class="btn btn-sm btn-outline-primary">
                                            Liên hệ người bán
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <div class="order-items mb-2">
                                <ul class="list-unstyled mb-0">
                                    @foreach ($order->orderDetails as $item)
                                        @php
                                            $album = $item->variant->album ?? '';
                                            $images = $album ? explode(',', $album) : [];
                                            $firstImage = count($images) > 0 ? $images[0] : $item->product->image;
                                        @endphp
                                        <li class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="product-order-img">
                                                    <img src="{{ asset($firstImage) }}" alt="">
                                                </div>
                                                <div class="product-order-info">
                                                    <div class="product-name">{{ $item->product->translation->name }}</div>
                                                    @if ($item->variant)
                                                        <div class="product-variant text-muted small">
                                                            {{ $item->variant->translation->name }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                @if ($item->discount != 0)
                                                    <div class="product-price text-muted">
                                                        <s>{{ number_format($item->price, 0, ',', '.') }}₫</s>
                                                    </div>
                                                @endif
                                                <div class="product-final-price">
                                                    {{ number_format($item->final_price, 0, ',', '.') }}₫
                                                </div>
                                                <div class="product-quantity text-muted small">x{{ $item->quantity }}</div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <div class="order-total text-right font-weight-bold">
                                Tổng số tiền: <span
                                    class="text-primary">{{ number_format($order->total, 0, ',', '.') }}₫</span>
                            </div>
                        </div>
                    @empty
                        @include('frontend.component.order.emptyOrder')
                    @endforelse
                </div>
                <!-- Đã giao (delivered) -->
                <div class="tab-pane" id="tabs-3" role="tabpanel">
                    @forelse ($orders->where('order_status', 'delivered') as $order)
                        <div class="order-card mb-3 p-3 shadow-sm rounded">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="order-header mb-1 d-flex align-items-center">
                                    <span class="badge badge-success mr-1">Hoàn thành</span>
                                    <strong>#{{ $order->id }}</strong>
                                    <span class="text-muted ml-2">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                <div>
                                    <form action="{{ route('order.reorder', $order->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Mua lại</button>
                                    </form>
                                </div>
                            </div>
                            <div class="order-items mb-2">
                                <ul class="list-unstyled mb-0">
                                    @foreach ($order->orderDetails as $item)
                                        @php
                                            $album = $item->variant->album ?? '';
                                            $images = $album ? explode(',', $album) : [];
                                            $firstImage = count($images) > 0 ? $images[0] : $item->product->image;
                                        @endphp
                                        <li class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="product-order-img">
                                                    <img src="{{ asset($firstImage) }}" alt="">
                                                </div>
                                                <div class="product-order-info">
                                                    <div class="product-name">{{ $item->product->translation->name }}
                                                    </div>
                                                    @if ($item->variant)
                                                        <div class="product-variant text-muted small">
                                                            {{ $item->variant->translation->name }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                @if ($item->discount != 0)
                                                    <div class="product-price">
                                                        {{ number_format($item->price, 0, ',', '.') }}₫
                                                    </div>
                                                @endif
                                                <div class="product-final-price">
                                                    {{ number_format($item->final_price, 0, ',', '.') }}₫
                                                </div>
                                                <div class="product-quantity text-muted small">x{{ $item->quantity }}
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <div class="order-total text-right font-weight-bold">
                                Tổng số tiền: <span
                                    class="text-primary">{{ number_format($order->total, 0, ',', '.') }}₫</span>
                            </div>
                        </div>
                    @empty
                        @include('frontend.component.order.emptyOrder')
                    @endforelse
                </div>
                <!-- Đã hủy (cancelled) -->
                <div class="tab-pane" id="tabs-4" role="tabpanel">
                    @forelse ($orders->where('order_status', 'cancelled') as $order)
                        <div class="order-card mb-3 p-3 shadow-sm rounded">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="order-header mb-1 d-flex align-items-center">
                                    <span class="badge badge-danger mr-1">Đã hủy</span>
                                    <strong>#{{ $order->id }}</strong>
                                    <span class="text-muted ml-2">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                <div>
                                    <form action="{{ route('order.reorder', $order->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Mua lại</button>
                                    </form>
                                </div>
                            </div>
                            <div class="order-items mb-2">
                                <ul class="list-unstyled mb-0">
                                    @foreach ($order->orderDetails as $item)
                                        @php
                                            $album = $item->variant->album ?? '';
                                            $images = $album ? explode(',', $album) : [];
                                            $firstImage = count($images) > 0 ? $images[0] : $item->product->image;
                                        @endphp
                                        <li class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="product-order-img">
                                                    <img src="{{ asset($firstImage) }}" alt="">
                                                </div>
                                                <div class="product-order-info">
                                                    <div class="product-name">{{ $item->product->translation->name }}
                                                    </div>
                                                    @if ($item->variant)
                                                        <div class="product-variant text-muted small">
                                                            {{ $item->variant->translation->name }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                @if ($item->discount != 0)
                                                    <div class="product-price">
                                                        {{ number_format($item->price, 0, ',', '.') }}₫
                                                    </div>
                                                @endif
                                                <div class="product-final-price">
                                                    {{ number_format($item->final_price, 0, ',', '.') }}₫
                                                </div>
                                                <div class="product-quantity text-muted small">x{{ $item->quantity }}
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="order-total text-right font-weight-bold">
                                Tổng số tiền: <span
                                    class="text-primary">{{ number_format($order->total, 0, ',', '.') }}₫</span>
                            </div>
                        </div>
                    @empty
                        @include('frontend.component.order.emptyOrder')
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
