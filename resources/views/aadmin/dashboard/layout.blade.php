<!DOCTYPE html>
<html>

<head>
    @include('aadmin.dashboard.component.head')
</head>

<body>
    <div id="wrapper">
        {{-- Sidebar --}}
        @include('aadmin.dashboard.component.sidebar')

        <div id="page-wrapper" class="gray-bg">
            {{-- Thanh navigation --}}
            @include('aadmin.dashboard.component.nav')

            {{-- Nội dung riêng của từng trang --}}
            <div class="wrapper wrapper-content">
                @yield('content')
            </div>

            {{-- Footer --}}
            @include('aadmin.dashboard.component.footer')
        </div>

        {{-- Nếu cần right sidebar thì bật lại --}}
        {{-- @include('aadmin.dashboard.component.right_sidebar') --}}
    </div>


    @include('aadmin.dashboard.component.scripts')
</body>

</html>
