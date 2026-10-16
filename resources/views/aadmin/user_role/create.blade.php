@extends('aadmin.dashboard.layout')
@section('content')
    @include('aadmin.dashboard.component.breadcrumb', ['title' => $config['seo']['create']['title']])
    <style>
        .ibox-content .form-control {
            margin-bottom: 20px;
            /* thêm khoảng cách dưới input */
        }
    </style>

    <form action="{{ route('user.role.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="wrapper wrapper-content animated fadeInRight">

            <!-- Thông tin chung -->
            <div class="row mb-4">
                <!-- Bên trái -->
                <div class="col-lg-5">
                    <div class="panel-head">
                        <div class="panel-title">
                            <h3>Thông tin chung</h3>
                            <p>Nhập thông tin chung của nhóm</p>
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
                                    <label class="control-label">Tên vai trò <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name" placeholder="Nhập tên"
                                        value="{{ old('name') }}">
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="control-label">Mô tả <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="description" placeholder="Nhập mô tả">{{ old('description') }}</textarea>
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
