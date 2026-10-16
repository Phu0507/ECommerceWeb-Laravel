@extends('aadmin.dashboard.layout')
@section('content')
    @include('aadmin.dashboard.component.breadcrumb', ['title' => $config['seo']['create']['title']])
    <style>
        .ibox-content .form-control {
            margin-bottom: 20px;
            /* thêm khoảng cách dưới input */
        }
    </style>


    <form action="{{ route('language.update', $language->id) }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="wrapper wrapper-content animated fadeInRight">

            <!-- Thông tin chung -->
            <div class="row mb-4">
                <!-- Bên trái -->
                <div class="col-lg-5">
                    <div class="panel-head">
                        <div class="panel-title">
                            <h3>Thông tin chung</h3>
                            <p>Nhập thông tin chung của ngôn ngữ</p>
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
                                    <label class="control-label">Tên ngôn ngữ<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name"
                                        placeholder="Nhập tên ngôn ngữ" value="{{ old('name') ?: $language->name }}">
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="control-label">Canonical <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="canonical" placeholder="Nhập canonical"
                                        value="{{ old('canonical') ?: $language->canonical }}">
                                </div>

                                <div class="col-lg-6 mb-3">
                                    <label class="control-label">Ảnh đại diện <span class="text-danger">*</span></label>

                                    {{-- Input chọn ảnh --}}
                                    <input type="file" class="form-control" name="image" accept="image/*"
                                        id="imageInput">

                                    {{-- Ảnh preview --}}
                                    <div class="mt-2">
                                        <img id="imagePreview" src="{{ $language->image ? asset($language->image) : '' }}"
                                            alt="Preview" width="100"
                                            style="{{ $language->image ? '' : 'display:none;' }}">
                                    </div>
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

    <script>
        document.getElementById('imageInput').addEventListener('change', function(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('imagePreview');

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            } else {
                // Nếu bỏ chọn ảnh, ẩn preview
                preview.src = '';
                preview.style.display = 'none';
            }
        });
    </script>


@endsection
