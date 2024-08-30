<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    @include("layouts.includes.head")
</head>
<body class="skin-blue grey-background">
<div id="app">
    @yield('content')
</div>

@include("layouts.includes.footer")
</body>
</html>
