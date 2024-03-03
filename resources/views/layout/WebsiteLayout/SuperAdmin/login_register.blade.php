<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title')</title>

    <link href="{{ asset('storage/WebsiteAsset/SuperAdmin/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('storage/WebsiteAsset/SuperAdmin/font-awesome/css/font-awesome.css') }}" rel="stylesheet">
    <link href="{{ asset('storage/WebsiteAsset/SuperAdmin/css/plugins/iCheck/custom.css') }}" rel="stylesheet">
    <link href="{{ asset('storage/WebsiteAsset/SuperAdmin/css/animate.css') }}" rel="stylesheet">
    <link href="{{ asset('storage/WebsiteAsset/SuperAdmin/css/style.css') }}" rel="stylesheet">
    @yield('links')
</head>

<body class="gray-bg">

    <div class="middle-box text-center loginscreen   animated fadeInDown">
        @yield('content')
    </div>

    <!-- Mainly scripts -->
    <script src="{{ asset('storage/WebsiteAsset/SuperAdmin/js/jquery-3.1.1.min.js') }}"></script>
    <script src="{{ asset('storage/WebsiteAsset/SuperAdmin/js/popper.min.js') }}"></script>
    <script src="{{ asset('storage/WebsiteAsset/SuperAdmin/js/bootstrap.js') }}"></script>
    @yield('script')
</body>

</html>
