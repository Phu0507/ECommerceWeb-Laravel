<style>
    .switchery-small {
        width: 33px;
        height: 20px;
        border-radius: 20px;
    }

    .switchery-small>small {
        width: 20px;
        height: 20px;

    }
</style>

<table class="table table-striped table-bordered table-hover dataTables-example">
    <thead>
        <tr>
            <th class="text-center">Mã đơn</th>
            <th class="text-center">Ngày tạo</th>
            <th class="text-center">Khách hàng</th>
            <th class="text-center">Hình thức thanh toán</th>
            <th class="text-center">Trạng thái thanh toán</th>
            <th class="text-center">Trạng thái đơn hàng</th>
            <th class="text-center">Tổng tiền</th>
            <th class="text-center">Hành động</th>
            <th class="text-center">Chi tiết</th>
        </tr>
    </thead>
    <tbody>
        @if (isset($orders) && $orders->count() > 0)
            @foreach ($orders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $order->customer_name }}: {{ $order->customer_phone }}</td>
                    <td class="text-center">
                        @switch($order->payment_method)
                            @case('cod')
                                <span class="badge bg-secondary">COD</span>
                            @break

                            @case('vnpay')
                                <span class="badge bg-success">VNPAY</span>
                            @break

                            @default
                                <span class="badge bg-secondary">Lỗi</span>
                        @endswitch
                    </td>
                    <td class="text-center">
                        @switch($order->payment_status)
                            @case('paid')
                                <span class="badge bg-primary">Đã thanh toán</span>
                            @break

                            @case('unpaid')
                                <span class="badge bg-danger text-dark">Chưa thanh toán</span>
                            @break

                            @case('refunded')
                                <span class="badge bg-success text-dark">Đã hoàn tiền</span>
                            @break

                            @case('pending')
                                <span class="badge bg-warning text-dark">Đang thanh toán</span>
                            @break

                            @default
                                <span class="badge bg-secondary">Lỗi</span>
                        @endswitch
                    </td>
                    <td class="text-center" id="status-{{ $order->id }}">
                        @include('aadmin.order.component.status_badge', ['order' => $order])
                    </td>
                    <td class="text-right">{{ number_format($order->total, 0, ',', '.') }}₫</td>
                    <td class="text-center" id="actions-{{ $order->id }}">
                        @include('aadmin.order.component.actions', ['order' => $order])
                    </td>
                    <td class="text-center">
                        <a href="{{ route('orders.show', $order->id) }}" class="btn btn-xs btn-success"
                            style="text-align: center">
                            <i class="fa fa-eye"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
{{ $orders->links('pagination::bootstrap-5') }}
