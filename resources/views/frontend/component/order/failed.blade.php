@extends('frontend.index')

@section('content')
    @include('frontend.component.search')
    <div class="order-result-wrap">
        <div class="order-result-card failed">
            <div class="icon-wrap">
                <svg class="icon icon-cross" viewBox="0 0 120 120" width="120" height="120" aria-hidden="true">
                    <circle class="circle" cx="60" cy="60" r="54" fill="none" stroke-width="6" />
                    <path class="cross1" d="M40 40 L80 80" fill="none" stroke-width="6" stroke-linecap="round" />
                    <path class="cross2" d="M80 40 L40 80" fill="none" stroke-width="6" stroke-linecap="round" />
                </svg>
            </div>

            <h1>Thanh toán thất bại</h1>
            <p class="lead">
                Đơn hàng <strong>#{{ $order->id ?? '' }}</strong> chưa được thanh toán. Vui lòng thử lại hoặc liên hệ
                bộ phận hỗ trợ nếu cần.
            </p>

            <div class="actions">
                <a href="{{ route('checkout.index') }}" class="btn btn-primary">Thử lại thanh toán</a>
                <a href="{{ route('home.index') }}" class="btn btn-outline">Về trang chủ</a>
            </div>
        </div>
    </div>
@endsection
