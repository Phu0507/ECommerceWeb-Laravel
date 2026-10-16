@extends('aadmin.dashboard.layout')
@section('content')
    @include('aadmin.dashboard.component.breadcrumb', [
        'title' => $config['seo']['order']['detail']['title'],
    ])

    <div class="container mt-4">
        <div class="row">
            <!-- Thông tin đơn hàng -->
            <div class="col-md-6">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Thông tin đơn hàng</h5>
                    </div>
                    <div class="ibox-content">
                        <p><strong>Mã đơn:</strong> #{{ $order->id }}</p>
                        <p><strong>Ngày tạo:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                        <p><strong>Hình thức thanh toán:</strong> <span
                                class="badge bg-secondary">{{ $order->payment_method }}</span></p>
                        <div class="row">
                            <!-- Trạng thái đơn hàng -->
                            <div class="col-md-6 mb-3">
                                <label for="order_status" class="form-label"><strong>Trạng thái đơn
                                        hàng:</strong></label>
                                <div id="order-status-wrapper">
                                    <span id="order-status-badge">
                                        @if ($order->order_status === 'pending')
                                            <span class="badge bg-warning">Chờ xác nhận</span>
                                        @elseif($order->order_status === 'confirmed')
                                            <span class="badge bg-success">Đã xác nhận</span>
                                        @elseif($order->order_status === 'shipping')
                                            <span class="badge bg-info">Đang giao</span>
                                        @elseif($order->order_status === 'delivered')
                                            <span class="badge bg-primary">Hoàn thành</span>
                                        @elseif($order->order_status === 'cancelled')
                                            <span class="badge bg-danger">Đã hủy</span>
                                        @endif
                                    </span>
                                    <button type="button" id="btn-edit-status" class="btn btn-sm btn-outline-primary">Thay
                                        đổi</button>
                                </div>

                                {{-- Select + nút Lưu/Hủy (ẩn mặc định) --}}
                                <div id="order-status-edit" style="display: none;">
                                    <div class="form-inline">
                                        <select name="order_status" id="order_status" class="form-control input-sm">
                                            <option value="pending"
                                                {{ $order->order_status === 'pending' ? 'selected' : '' }}
                                                {{ in_array($order->order_status, ['confirmed', 'shipping', 'delivered', 'cancelled']) ? 'disabled' : '' }}>
                                                Chờ xác nhận
                                            </option>
                                            <option value="confirmed"
                                                {{ $order->order_status === 'confirmed' ? 'selected' : '' }}
                                                {{ in_array($order->order_status, ['shipping', 'delivered', 'cancelled']) ? 'disabled' : '' }}>
                                                Đã xác nhận
                                            </option>
                                            <option value="shipping"
                                                {{ $order->order_status === 'shipping' ? 'selected' : '' }}
                                                {{ in_array($order->order_status, ['delivered', 'cancelled']) ? 'disabled' : '' }}>
                                                Đang giao
                                            </option>
                                            <option value="delivered"
                                                {{ $order->order_status === 'delivered' ? 'selected' : '' }}
                                                {{ $order->order_status === 'cancelled' ? 'disabled' : '' }}>
                                                Hoàn thành
                                            </option>
                                            <option value="cancelled"
                                                {{ $order->order_status === 'cancelled' ? 'selected' : '' }}
                                                {{ in_array($order->order_status, ['delivered']) ? 'disabled' : '' }}>
                                                Đã hủy
                                            </option>
                                        </select>
                                        <button type="button" id="btn-save-status" class="btn btn-sm btn-primary"
                                            style="margin-left:5px;">Lưu</button>
                                        <button type="button" id="btn-cancel-status" class="btn btn-sm btn-default"
                                            style="margin-left:5px;">Hủy</button>
                                    </div>
                                </div>
                            </div>
                            <!-- Trạng thái thanh toán -->
                            <div class="col-md-6 mb-3">
                                <label for="payment_status" class="form-label"><strong>Trạng thái thanh
                                        toán:</strong></label>
                                <div>
                                    @if ($order->payment_status === 'pending')
                                        <span class="badge bg-warning text-dark">Đang chờ</span>
                                    @elseif($order->payment_status === 'unpaid')
                                        <span class="badge bg-danger">Chưa thanh toán</span>
                                    @elseif($order->payment_status === 'paid')
                                        <span class="badge bg-primary">Đã thanh toán</span>
                                    @elseif($order->payment_status === 'refunded')
                                        <span class="badge bg-success">Đã hoàn tiền</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ibox-footer">
                        <h4 class="mt-1"><strong>Tổng tiền:</strong> {{ number_format($order->total, 0, ',', '.') }}đ
                        </h4>
                    </div>
                </div>
            </div>

            <!-- Thông tin khách hàng -->
            <div class="col-md-6">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Thông tin khách hàng</h5>
                    </div>
                    <div class="ibox-content">
                        <p><strong>Họ tên:</strong> {{ $order->customer_name }}</p>
                        <p><strong>Email:</strong> {{ $order->customer_email }}</p>
                        <p><strong>Số điện thoại:</strong> {{ $order->customer_phone }}</p>
                        <p><strong>Địa chỉ:</strong> {{ $order->customer_address }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sản phẩm -->
        <div class="ibox mt-4">
            <div class="ibox-title">
                <h5>Sản phẩm</h5>
            </div>
            <div class="ibox-content table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Ảnh</th>
                            <th>Sản phẩm</th>
                            <th>Số lượng</th>
                            <th>Giá gốc</th>
                            <th>Khuyến mãi</th>
                            <th>Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->orderDetails as $item)
                            <tr>
                                <td><img src="{{ asset($item->variant->album) }}" width="60"></td>
                                <td>
                                    {{ $item->product->translation->name }} <br>
                                    <span class="text-muted" style="font-style: italic;">
                                        {{ $item->variant->translation->name }}
                                    </span>
                                </td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ number_format($item->price, 0, ',', '.') }}đ</td>
                                <td>{{ number_format($item->discount, 0, ',', '.') }}đ</td>
                                <td>{{ number_format($item->quantity * $item->final_price, 0, ',', '.') }}đ</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <a href="{{ route('orders.index') }}" class="btn btn-secondary mt-3">← Quay lại</a>
    </div>
    <script>
        $(document).ready(function() {
            let oldStatus = "{{ $order->order_status }}";

            $("#btn-edit-status").click(function() {
                $("#order-status-wrapper").hide();
                $("#order-status-edit").show();
            });

            $("#btn-cancel-status").click(function() {
                $("#order_status").val(oldStatus); // reset lại
                $("#order-status-edit").hide();
                $("#order-status-wrapper").show();
            });

            $("#btn-save-status").click(function() {
                let newStatus = $("#order_status").val();
                $.ajax({
                    url: "{{ route('orders.updateStatus', $order->id) }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        status: newStatus
                    },
                    success: function(res) {
                        if (res.success) {
                            oldStatus = newStatus;
                            $("#order-status-badge").html(res.status_badge);
                            $("#order-status-edit").hide();
                            $("#order-status-wrapper").show();
                        }
                    },
                    error: function() {
                        alert("Có lỗi xảy ra khi cập nhật trạng thái.");
                    }
                });
            });
        });
    </script>
@endsection
