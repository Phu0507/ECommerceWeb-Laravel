@extends('aadmin.dashboard.layout')

@section('content')
    @include('aadmin.dashboard.component.breadcrumb', ['title' => $config['seo']['index']['title']])

    {{-- Tiêu đề trang --}}

    {{-- Nội dung chính của trang --}}

    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float element-toggled">
                    <div class="ibox-title">
                        <h5>{{ $config['seo']['index']['table_name'] }}</h5>
                        <div class="ibox-tools  pull-right">
                            <a class="btn btn-primary btn-xs" href="{{ route('post.create') }}">Thêm mới</a>
                        </div>
                    </div>
                    @error('error')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror

                    <div class="ibox-content">

                        @include('aadmin.post.post.component.post_filter')
                        @include('aadmin.post.post.component.post_table')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
