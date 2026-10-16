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
                        <div class="ibox-tools pull-right">
                            <a class="btn btn-success btn-sm " href="{{ route('user.role.permission') }}">Phân quyền</a>
                        </div>
                        <div class="ibox-tools pull-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('user.role.create') }}">Thêm mới</a>
                        </div>

                    </div>

                    <div class="ibox-content">

                        @include('aadmin.user_role.component.filter')
                        @include('aadmin.user_role.component.table')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
