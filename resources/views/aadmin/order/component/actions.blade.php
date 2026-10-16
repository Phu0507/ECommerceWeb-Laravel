@if ($order->order_status === 'pending')
    <button class="btn btn-xs btn-success btn-change-status" data-id="{{ $order->id }}" data-status="confirmed">Xác
        nhận</button>
    <button class="btn btn-xs btn-danger btn-change-status" data-id="{{ $order->id }}"
        data-status="cancelled">Hủy</button>
@elseif ($order->order_status === 'confirmed')
    <button class="btn btn-xs btn-info btn-change-status" data-id="{{ $order->id }}" data-status="shipping">Giao
        hàng</button>
    <button class="btn btn-xs btn-danger btn-change-status" data-id="{{ $order->id }}"
        data-status="cancelled">Hủy</button>
@elseif ($order->order_status === 'shipping')
    <button class="btn btn-xs btn-primary btn-change-status" data-id="{{ $order->id }}" data-status="delivered">Hoàn
        thành</button>
    <button class="btn btn-xs btn-danger btn-change-status" data-id="{{ $order->id }}"
        data-status="cancelled">Hủy</button>
@elseif ($order->order_status === 'delivered')
    <span class="badge bg-secondary">Hoàn tất</span>
@elseif ($order->order_status === 'cancelled')
    <span class="badge bg-secondary">Đơn đã hủy</span>
@endif
