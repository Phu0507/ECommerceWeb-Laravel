<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>INFINITY+ | Dashboard</title>

<link href="{{ asset('css2/css/bootstrap.min.css') }}" rel="stylesheet">
<link href="{{ asset('css2/font-awesome/css/font-awesome.css') }}" rel="stylesheet">

<link href="{{ asset('css2/css/animate.css') }}" rel="stylesheet">
<link href="{{ asset('css2/css/style.css') }}" rel="stylesheet">
<link href="{{ asset('css2/css/customize.css') }}" rel="stylesheet">
<script src="{{ asset('css2/js/jquery-3.1.1.min.js') }}"></script>

<!-- include summernote css/js -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.js"></script>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@if (isset($config['css']) && is_array($config['css']))
    @foreach ($config['css'] as $key => $value)
        {!! '<link href="' . asset($value) . '" rel="stylesheet">' !!}
    @endforeach
@endif
