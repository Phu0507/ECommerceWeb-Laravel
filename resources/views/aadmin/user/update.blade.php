@extends('aadmin.dashboard.layout')
@section('content')
    @include('aadmin.dashboard.component.breadcrumb', ['title' => $config['seo']['create']['title']])
    <style>
        .ibox-content .form-control {
            margin-bottom: 20px;
            /* thêm khoảng cách dưới input */
        }
    </style>


    <form action="{{ route('user.update', $user->id) }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="wrapper wrapper-content animated fadeInRight">

            <!-- Thông tin chung -->
            <div class="row mb-4">
                <!-- Bên trái -->
                <div class="col-lg-5">
                    <div class="panel-head">
                        <div class="panel-title">
                            <h3>Thông tin chung</h3>
                            <p>Nhập thông tin chung của người sử dụng</p>
                            <p class="mb-0">Lưu ý: Những trường đánh dấu <span class="text-danger">*</span> là bắt buộc</p>
                            @if ($errors->any())
                                <div class="alert alert-danger mt-2">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <!-- Bên phải -->
                <div class="col-lg-7">
                    <div class="ibox">
                        <div class="ibox-content">
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="control-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" name="email" placeholder="Nhập email"
                                        value="{{ old('email', $user->email) }}">
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="control-label">Họ tên <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name" placeholder="Nhập họ tên"
                                        value="{{ old('name', $user->name) }}">
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="control-label">Vai trò <span class="text-danger">*</span></label>
                                    <select class="form-control" name="role">
                                        <option value="" {{ old('role') === null ? 'selected' : '' }}>[Chọn Vai Trò]
                                        </option>
                                        <option value="0" {{ old('role', $user->role) == 0 ? 'selected' : '' }}>Quản
                                            trị viên
                                        </option>
                                        <option value="1" {{ old('role', $user->role) == 1 ? 'selected' : '' }}>Người
                                            dùng
                                        </option>
                                    </select>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="control-label">Ngày sinh</label>
                                    <input type="date" class="form-control" name="birthday"
                                        value="{{ old('birthday', date('Y-m-d', strtotime($user->birthday))) }}">

                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="control-label">Mật khẩu <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" name="password">
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="control-label">Nhập lại mật khẩu <span
                                            class="text-danger">*</span></label>
                                    <input type="password" class="form-control" name="password_confirmation">
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="control-label">Ảnh đại diện</label>
                                    <input type="file" class="form-control" name="image" accept="image/*">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Thông tin liên hệ -->
            <div class="row mb-4">
                <!-- Bên trái -->
                <div class="col-lg-5">
                    <div class="panel-head">
                        <div class="panel-title">
                            <h3>Thông tin liên hệ</h3>
                            <p>Nhập thông tin liên hệ của người sử dụng</p>
                        </div>
                    </div>
                </div>
                <!-- Bên phải -->
                <div class="col-lg-7">
                    <div class="ibox">
                        <div class="ibox-content">
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="control-label">Thành phố/Tỉnh</label>
                                    <select class="form-control province" name="province_id">
                                        <option value="" disabled selected hidden>[Chọn thành phố]</option>
                                        @foreach ($provinces as $province)
                                            <option value="{{ $province->matp }}"
                                                {{ old('province_id', $user->province_id) == $province->matp ? 'selected' : '' }}>
                                                {{ $province->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="control-label">Quận/Huyện</label>
                                    <select class="form-control district" name="district_id"
                                        data-selected-id="{{ old('district_id', $user->district_id) }}">
                                        <option value="" disabled hidden>[Chọn Quận/Huyện]</option>
                                    </select>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="control-label">Phường/Xã</label>
                                    <select class="form-control ward" name="ward_id"
                                        data-selected-id="{{ old('ward_id', $user->ward_id) }}">
                                        <option value="" disabled hidden>[Chọn Phường/Xã]</option>
                                    </select>

                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="control-label">Địa chỉ</label>
                                    <input type="text" class="form-control" name="address" placeholder="Nhập địa chỉ"
                                        value="{{ old('address', $user->address) }}">
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="control-label">Số điện thoại</label>
                                    <input type="text" class="form-control" name="phone"
                                        placeholder="Nhập số điện thoại" value="{{ old('phone', $user->phone) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nút Lưu -->
            <div class="row">
                <div class="col-lg-5"></div>
                <div class="col-lg-7">
                    <button type="submit" class="btn btn-primary">Lưu</button>
                    <a href="#" class="btn btn-secondary">Hủy</a>
                </div>
            </div>
        </div>
    </form>

@endsection
