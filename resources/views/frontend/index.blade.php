<!DOCTYPE html>
<html lang="zxx">

<head>
    @include('frontend.component.head')
</head>

<body>
    @include('frontend.component.header')
    @yield('content')
    @include('frontend.component.footer')
    @include('frontend.component.scripts')
</body>

</html>
