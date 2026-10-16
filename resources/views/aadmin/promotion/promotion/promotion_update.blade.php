@extends('aadmin.dashboard.layout')
@section('content')
    @include('aadmin.dashboard.component.breadcrumb', ['title' => $config['seo']['create']['title']])
    <style>
        .ibox-content .form-control {
            margin-bottom: 20px;
            /* thêm khoảng cách dưới input */
        }

        .meta-title {
            font-size: 20px;
            color: #1a0dab;
        }

        .meta-description {
            font-size: 14px;
            color: #666;
        }

        .canonical {
            font-size: 12px;
            color: green;
            margin-bottom: 8px;
        }

        .seo-wrapper {
            margin-top: 20px;
        }

        .seo-wrapper textarea {
            height: 200px;
        }
    </style>


    <form action="{{ route('promotion.update', $promotions->id) }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="wrapper wrapper-content animated fadeInRight">
            @if ($errors->any())
                <div class="alert alert-danger mt-2">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="row">
                <div class="col-lg-8">
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>Thông tin chung</h5>
                        </div>
                        <div class="ibox-content">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-row">
                                        <label class="control-label text-left">Mã chương trình<span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="code"
                                            value="{{ old('code', $promotions->code ?? '') }}" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-row">
                                        <label class="control-label text-left">Tên chương trình<span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="name"
                                            value="{{ old('name', $promotions->name ?? '') }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-row">
                                        <label class="control-label text-left">Mô tả</label>
                                        <textarea class="form-control" name="description" rows="3">{{ old('description', $promotions->description ?? '') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>Cài đặt thông tin khuyến mãi</h5>
                        </div>
                        <div class="ibox-content">
                            <div class="form-row">
                                <label class="control-label text-left">Chọn hình thức<span
                                        class="text-danger">*</span></label>
                                <select class="form-control promotion-select" name="promotion_type" required>
                                    <option value="">-- Chọn hình thức --</option>
                                    <option value="order"
                                        {{ old('promotion_type', $promotions->promotion_type ?? '') == 'order' ? 'selected' : '' }}>
                                        Giảm giá
                                        theo tổng giá trị đơn hàng</option>
                                    <option value="product"
                                        {{ old('promotion_type', $promotions->promotion_type ?? '') == 'product' ? 'selected' : '' }}>
                                        Giảm giá theo từng sản phẩm</option>
                                </select>
                            </div>
                            <div class="promotion-container" id="promotion_custom_box">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>Thời gian áp dụng chương trình</h5>
                        </div>
                        <div class="ibox-content">
                            <div class="form-row">
                                <label class="control-label text-left">Từ ngày<span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control start-date" name="start_date"
                                    value="{{ old('start_date', $promotions->start_date ?? '') }}" required>
                            </div>
                            <div class="form-row">
                                <label class="control-label text-left">Đến ngày<span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control end-date" name="end_date"
                                    value="{{ old('end_date', $promotions->end_date ?? '') }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>Đối tượng áp dụng</h5>
                        </div>
                        <div class="ibox-content">
                            <div class="setting-value">
                                <div style="display:flex;align-items:center;margin-bottom:8px;">
                                    <input type="radio" name="customer_option" id="all_customers" value="all"
                                        style="margin-right:8px;width:18px;height:18px;" class="choose-option"
                                        {{ old('customer_option', $promotions->customer_option ?? '') == 'all' ? 'checked' : '' }}>
                                    <label for="all_customers" style="margin:0;line-height:1;cursor:pointer;">
                                        Toàn bộ khách hàng
                                    </label>
                                </div>
                                <div style="display:flex;align-items:center;margin-bottom:8px;">
                                    <input type="radio" name="customer_option" id="custom_customers" value="custom"
                                        style="margin-right:8px;width:18px;height:18px;" class="choose-option"
                                        {{ old('customer_option', $promotions->customer_option ?? '') == 'custom' ? 'checked' : '' }}>
                                    <label for="custom_customers" style="margin:0;line-height:1;cursor:pointer;">
                                        Tự chọn đối tượng khách hàng
                                    </label>
                                </div>
                                <!-- Select ẩn mặc định -->
                                <div id="customer_select_box"
                                    style="{{ old('customer_option', $promotions->customer_option ?? '') == 'custom' ? '' : 'display:none;' }}; margin-top:10px;">
                                    <label for="customer_select" style="display:block; margin-bottom:5px;">Chọn khách
                                        hàng:</label>
                                    <select id="customer_select" name="customer_id[]" class="form-control select2-customer"
                                        multiple>

                                        {{-- Nhóm khách hàng --}}
                                        <option value="user_role"
                                            {{ in_array('user_role', old('customer_id', collect($promotions->customerConditions)->filter(fn($c) => !empty($c->user_role_id))->map(fn($c) => 'user_role')->toArray())) ? 'selected' : '' }}>
                                            Nhóm khách hàng
                                        </option>

                                        {{-- Giới tính --}}
                                        <option value="gender"
                                            {{ in_array('gender', old('customer_id', collect($promotions->customerConditions)->filter(fn($c) => !empty($c->gender))->map(fn($c) => 'gender')->toArray())) ? 'selected' : '' }}>
                                            Giới tính
                                        </option>

                                        {{-- Độ tuổi --}}
                                        <option value="age_group"
                                            {{ in_array('age_group', old('customer_id', collect($promotions->customerConditions)->filter(fn($c) => !empty($c->age_group))->map(fn($c) => 'age_group')->toArray())) ? 'selected' : '' }}>
                                            Độ tuổi
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="customer_custom_boxes"></div>
                </div>
            </div>
            <!-- Nút Lưu -->
            <div class="row">
                <div class="col-lg-9">
                    <button type="submit" class="btn btn-primary">Lưu</button>
                    <a href="#" class="btn btn-secondary">Hủy</a>
                </div>
                <div class="col-lg-3">

                </div>
            </div>
        </div>
    </form>
    <script>
        var oldUserRoles = @json(old('user_role_id', $promotions->customerConditions->pluck('user_role_id')->filter()->toArray() ?? []));
        var oldGenders = @json(old('gender', $promotions->customerConditions->pluck('gender')->filter()->toArray() ?? []));
        var oldAgeGroups = @json(old('age_group', $promotions->customerConditions->pluck('age_group')->filter()->toArray() ?? []));
        var promotionProducts = @json($promotions->products->toArray() ?? []);
        var promotionDiscounts = @json($promotions->discounts->toArray() ?? []);
        var discountType = @json($promotions->discount_type);
    </script>

@endsection
