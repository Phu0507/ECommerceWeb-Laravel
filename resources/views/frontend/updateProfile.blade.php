@extends('frontend.index')

@section('content')
    @include('frontend.component.search')

    <!-- Breadcrumb -->
    <section class="breadcrumb-section set-bg" data-setbg="{{ asset('frontend2/img/breadcrumbs-bg.jpg') }}">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="breadcrumb__text">
                        <h2>Thông tin cá nhân</h2>
                        <div class="breadcrumb__option">
                            <a href="{{ route('home.index') }}">Trang chủ</a>
                            <span>Cập nhật thông tin</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Profile section -->
    <section class="profile-section spad">
        <div class="container-fluid">
            <div class="card shadow-sm p-4">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <!-- Horizontal form fields -->
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Họ và tên <span class="text-danger">*</span></label>
                                <div class="col-sm-8">
                                    <input type="text" name="name" class="form-control"
                                        value="{{ old('name', $user->name) }}" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Email <span class="text-danger">*</span></label>
                                <div class="col-sm-8">
                                    <input type="email" name="email" class="form-control"
                                        value="{{ old('email', $user->email) }}" readonly>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Ngày sinh</label>
                                <div class="col-sm-8">
                                    <input type="date" name="birthday" class="form-control"
                                        value="{{ old('birthday', $user->birthday ? date('Y-m-d', strtotime($user->birthday)) : '') }}">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Số điện thoại</label>
                                <div class="col-sm-8">
                                    <input type="text" name="phone" class="form-control"
                                        value="{{ old('phone', $user->phone) }}" placeholder="Nhập số điện thoại">
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Thành phố/Tỉnh</label>
                                <div class="col-sm-8">
                                    <input type="text" name="province_id" class="form-control"
                                        value="{{ old('province_id', $user->province_id) }}"
                                        placeholder="Nhập tỉnh/Thành phố">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Quận/Huyện</label>
                                <div class="col-sm-8">
                                    <input type="text" name="district_id" class="form-control"
                                        value="{{ old('district_id', $user->district_id) }}" placeholder="Nhập huyện">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Phường/Xã</label>
                                <div class="col-sm-8">
                                    <input type="text" name="ward_id" class="form-control"
                                        value="{{ old('ward_id', $user->ward_id) }}" placeholder="Nhập phường/xã">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Địa chỉ</label>
                                <div class="col-sm-8">
                                    <input type="text" name="address" class="form-control"
                                        value="{{ old('address', $user->address) }}" placeholder="Nhập địa chỉ chi tiết">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-danger px-5">Lưu thay đổi</button>
                        <a href="{{ route('home.index') }}" class="btn btn-secondary px-4 ml-2">Quay về trang chủ</a>
                    </div>
                </form>
            </div>
        </div>
    </section>

@endsection
