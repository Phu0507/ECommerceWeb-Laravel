<!-- Mainly scripts -->
<script src="{{ asset('css2/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('css2/js/plugins/metisMenu/jquery.metisMenu.js') }}"></script>
<script src="{{ asset('css2/js/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>
<script src="{{ asset('css2/library/library.js') }}"></script>

<script src="{{ asset('css2/js/inspinia.js') }}"></script>
<script src="{{ asset('css2/js/plugins/pace/pace.min.js') }}"></script>

<!-- jQuery UI -->
<script src="{{ asset('css2/js/plugins/jquery-ui/jquery-ui.min.js') }}"></script>

@if (isset($config['js']) && is_array($config['js']))
    @foreach ($config['js'] as $key => $value)
        {!! '<script src="' . asset($value) . '"></script>' !!}
    @endforeach
@endif
