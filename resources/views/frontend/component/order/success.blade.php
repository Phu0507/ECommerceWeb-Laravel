@extends('frontend.index')

@section('content')
    @include('frontend.component.search')
    <div class="order-result-wrap">
        <div class="order-result-card success">
            <div class="icon-wrap">
                <svg class="icon icon-check" viewBox="0 0 120 120" width="120" height="120" aria-hidden="true">
                    <circle class="circle" cx="60" cy="60" r="54" fill="none" stroke-width="6" />
                    <path class="check" fill="none" stroke-width="6" d="M34 62 L52 80 L86 44" />
                </svg>
            </div>
            <h1>Đặt hàng thành công!</h1>
            <p class="lead">
                Cảm ơn bạn. Đơn hàng <strong>#{{ $order->id ?? '' }}</strong> đã được tạo thành công.
                @if (isset($order) && $order->payment_method === 'vnpay')
                    Thanh toán sẽ được cập nhật ngay khi VNPAY xác nhận.
                @endif
            </p>

            <div class="actions">
                <a href="{{ route('home.index') }}" class="btn btn-primary">Về trang chủ</a>
                <a href="{{ route('order.index') }}" class="btn btn-outline">Xem chi tiết đơn</a>
            </div>
        </div>
    </div>
@endsection
