<div class="ibox">
    <div class="ibox-content">
        <div class="row mb-15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label class="control-label text-left">Chọn danh mục cha <span class="text-danger"
                            style="font-size: 12px">(mặc định là root)</span></label>

                    @php
                        $selectedParentId = old('category_id', $categoryOptions['category_id'] ?? 0);
                    @endphp

                    <select class="form-control" name="category_id">
                        <option value="0" {{ $selectedParentId == 0 ? 'selected' : '' }}>Root</option>
                        @foreach ($categoryList as $id => $name)
                            <option value="{{ $id }}" {{ $selectedParentId == $id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>

                </div>
            </div>
        </div>
    </div>
</div>
<div class="ibox">
    <div class="ibox-content">
        <div class="row mb-15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label class="control-label text-left">Nhập giá chung</label>
                    <input type="number" class="form-control" name="base_price"
                        value="{{ old('base_price') ?? (isset($categoryOptions) ? $categoryOptions['base_price'] ?? '' : '') }}">
                </div>
                <div class="form-row">
                    <label class="control-label text-left">Nhập số lượng<span class="text-danger"
                            style="font-size: 12px"> (chỉ dùng khi sản phẩm không có biến thể)</span></label>
                    <input type="number" class="form-control" name="base_quantity"
                        value="{{ old('base_quantity') ?? (isset($categoryOptions) ? $categoryOptions['base_quantity'] ?? '' : '') }}">
                </div>
            </div>
        </div>
    </div>
</div>
<div class="ibox">
    <div class="ibox-content">
        <div class="row mb-15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label class="control-label text-left">Chọn tình trạng<span class="text-danger">*</span></label>
                    @php
                        $selectedPublish = old('status') ?? ($categoryOptions['status'] ?? '0');
                    @endphp
                    <select class="form-control" name="status">
                        <option value="0" {{ $selectedPublish == '0' ? 'selected' : '' }}>Chọn tình trạng</option>
                        <option value="1" {{ $selectedPublish == '1' ? 'selected' : '' }}>Công khai</option>
                        <option value="2" {{ $selectedPublish == '2' ? 'selected' : '' }}>Riêng tư</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="ibox">
    <div class="ibox-content">
        <div class="row mb-15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label class="control-label">Ảnh đại diện</label>
                    <input type="file" class="form-control" name="image" accept="image/*" id="imageInput">

                    <!-- Ảnh preview -->
                    <img id="imagePreview"
                        src="{{ isset($categoryOptions) && $categoryOptions['image'] ? asset($categoryOptions['image']) : '' }}"
                        alt="Xem trước ảnh" class="mt-2"
                        style="max-width: 200px; {{ isset($categoryOptions) && $categoryOptions['image'] ? '' : 'display: none;' }} border: 1px solid #ccc; padding: 5px;">
                </div>
            </div>
        </div>
    </div>
</div>
