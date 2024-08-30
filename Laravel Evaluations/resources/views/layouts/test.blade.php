<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    @include("layouts.includes.head")
	<meta name="google" content="notranslate" />
    <script src="https://cdn.jsdelivr.net/npm/promise-polyfill@7.1.0/dist/promise.min.js"></script>
</head>
<body class="skin-blue grey-background tests-layout @yield('sectionClass')">
<div id="app">

    <div class="sticky-test">
        <div class="row">
            <div class="col-xs-4">
                <img id="logo-sticky" src="{{ asset('assets/img/logo-200x65.png') }}" class="logo-lg">
            </div>
            <div class="col-xs-4"></div>
            <div class="col-xs-4">
                <div class="timer_div hidden">
                    <div class="timer">
                    </div>
                    <div class="time_left">Time left</div>
                </div>
            </div>
        </div>

    </div>

    @yield('content')
</div>

@include("layouts.includes.footer")
@yield('footer')
</body>
</html>
