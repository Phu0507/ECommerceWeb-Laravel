<form action="{{ route('orders.index') }}" method="GET" class="filter" style="margin-bottom: 15px;">
    <div class="uk-flex uk-flex-middle uk-flex-wrap" style="gap: 8px;">
        <!-- Chọn số lượng bản ghi -->
        <select name="perpage" class="form-control input-sm mr10" style="display:inline-block; width:auto;"
            onchange="this.form.submit()">
            <option value="10" {{ request('perpage') == 10 ? 'selected' : '' }}>10 bản ghi</option>
            <option value="25" {{ request('perpage') == 25 ? 'selected' : '' }}>25 bản ghi</option>
            <option value="50" {{ request('perpage') == 50 ? 'selected' : '' }}>50 bản ghi</option>
            <option value="100" {{ request('perpage') == 100 ? 'selected' : '' }}>100 bản ghi</option>
        </select>

        <!-- Chọn tình trạng -->
        <select name="order_status" class="form-control input-sm mr10" style="display:inline-block; width:auto;">
            <option value="">-- Trạng thái đơn hàng --</option>
            <option value="pending" {{ request('order_status') == 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
            <option value="confirmed" {{ request('order_status') == 'confirmed' ? 'selected' : '' }}>Xác nhận
            </option>
            <option value="shipping" {{ request('order_status') == 'shipping' ? 'selected' : '' }}>Đang giao
            </option>
            <option value="delivered" {{ request('order_status') == 'delivered' ? 'selected' : '' }}>Hoàn thành</option>
            <option value="cancelled" {{ request('order_status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
        </select>

        <select name="payment_status" class="form-control input-sm mr10" style="display:inline-block; width:auto;">
            <option value="">-- Trạng thái thanh toán --</option>
            <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Chưa thanh toán
            </option>
            <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
            <option value="refunded" {{ request('payment_status') == 'refunded' ? 'selected' : '' }}>Đã hoàn tiền
            </option>
        </select>

        <input type="text" name="search" class="form-control input-sm mr10" placeholder="Nhập từ khóa tìm kiếm..."
            value="{{ request('search') }}" style="display:inline-block; width:200px;">

        <button class="btn btn-sm btn-primary">Lọc</button>
    </div>
</form>
