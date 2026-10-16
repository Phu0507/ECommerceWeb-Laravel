@switch($order->order_status)
    @case('pending')
        <span class="badge bg-warning text-dark">Chờ xác nhận</span>
    @break

    @case('confirmed')
        <span class="badge bg-success">Đã xác nhận</span>
    @break

    @case('shipping')
        <span class="badge bg-info text-dark">Đang giao</span>
    @break

    @case('delivered')
        <span class="badge bg-primary">Hoàn thành</span>
    @break

    @case('cancelled')
        <span class="badge bg-danger">Đã hủy</span>
    @break

    @default
        <span class="badge bg-secondary">Lỗi</span>
@endswitch
