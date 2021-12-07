<!DOCTYPE html>
<html lang="zh">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>
    <!-- font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600">
    <link rel="stylesheet" href="{{ mix('css/fontawesome.min.css') }}">
    <!-- bootstrap -->
    <link rel="stylesheet" href="{{ mix('css/bootstrap.min.css') }}">
    <!-- 模版 -->
    <link rel="stylesheet" href="{{ mix('css/tooplate.css') }}">
    <!-- 日期 -->
    <link rel="stylesheet" href="{{ mix('css/bootstrap-datepicker.css') }}">
    @yield('css')
</head>

<body class="bg02">
    <div class="container">
        @include('layouts.header')
        @yield('content')
        @include('layouts.footer')
    </div>
</body>
<script src="{{ mix('js/jquery-3.3.1.min.js') }}"></script>
<script src="{{ mix('js/popper.min.js') }}"></script>
<script src="{{ mix('js/bootstrap.min.js') }}"></script>
<script src="{{ mix('js/common.js') }}"></script>
<!-- 日期 -->
<script src="{{ mix('js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ mix('js/bootstrap-datepicker.zh-TW.min.js') }}"></script>
@yield('script')
</html>