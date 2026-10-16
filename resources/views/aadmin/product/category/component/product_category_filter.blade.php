<form action="{{ route('product.category.index') }}" method="GET" class="filter" style="margin-bottom: 15px;">
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
        <select name="status" class="form-control input-sm mr10" style="display:inline-block; width:auto;">
            <option value="">Tất cả tình trạng</option>
            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Công khai</option>
            <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Riêng tư</option>
        </select>

        <!-- Ô tìm kiếm -->
        <input type="text" name="search" class="form-control input-sm mr10" placeholder="Nhập từ khóa tìm kiếm..."
            value="{{ request('search') }}" style="display:inline-block; width:200px;">

        <!-- Nút lọc -->
        <button class="btn btn-sm btn-primary">Lọc</button>
    </div>
</form>
