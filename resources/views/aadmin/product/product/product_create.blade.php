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

    <form action="{{ route('product.store') }}" method="post" enctype="multipart/form-data">
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
            <!-- Thông tin chung -->
            <div class="row mb-4">
                <!-- Bên trái -->
                <div class="col-lg-9">
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>Thông tin chung</h5>
                        </div>
                        <div class="ibox-content">
                            @include('aadmin.product.product.component.product_general')
                        </div>
                    </div>
                    @include('aadmin.product.product.component.product_variant')
                    @include('aadmin.product.product.component.product_seo')
                </div>
                <!-- Bên phải -->
                <div class="col-lg-3">
                    @include('aadmin.product.product.component.product_aside')
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
                preview.src = "";
                preview.style.display = 'none';
            }
        });
    </script>
@endsection
