@extends('aadmin.dashboard.layout')

@section('content')
    @include('aadmin.dashboard.component.breadcrumb', [
        'title' => $config['seo']['order']['index']['title'],
    ])
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float element-toggled">
                    <div class="ibox-title">
                        <h5>{{ $config['seo']['order']['index']['table_name'] }}</h5>
                    </div>
                    @error('error')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror

                    <div class="ibox-content">

                        @include('aadmin.order.component.order_filter')
                        @include('aadmin.order.component.order_table')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
