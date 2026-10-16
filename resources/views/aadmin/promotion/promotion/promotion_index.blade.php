@extends('aadmin.dashboard.layout')

@section('content')
    @include('aadmin.dashboard.component.breadcrumb', [
        'title' => $config['seo']['marketing']['index']['title'],
    ])
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float element-toggled">
                    <div class="ibox-title">
                        <h5>{{ $config['seo']['marketing']['index']['table_name'] }}</h5>
                        <div class="ibox-tools  pull-right">
                            <a class="btn btn-primary btn-xs" href="{{ route('promotion.create') }}">Thêm mới</a>
                        </div>
                    </div>
                    @error('error')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror

                    <div class="ibox-content">

                        @include('aadmin.promotion.promotion.component.promotion_filter')
                        @include('aadmin.promotion.promotion.component.promotion_table')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
