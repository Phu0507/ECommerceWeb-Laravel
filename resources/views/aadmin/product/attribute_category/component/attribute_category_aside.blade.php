<div class="ibox">
    <div class="ibox-content">
        <div class="row mb-15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label class="control-label text-left">Chọn danh mục cha</label>
                    <span class="text-danger" style="font-size: 12px">(mặc định là root)</span>
                    @php
                        $selectedParentId = old('parent_id', $categoryOptions?->parent_id ?? 0);
                    @endphp

                    <select class="form-control" name="parent_id">
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
                    <label class="control-label text-left">Chọn tình trạng<span class="text-danger">*</span></label>
                    @php
                        $selectedPublish = old('status') ?? ($categoryOptions->status ?? '0');
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
