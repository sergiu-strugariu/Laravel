<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    @include("layouts.includes.head")
    @yield('header')
</head>
<body class="skin-black sidebar-mini @if(isset($sidebarCollapsed) && $sidebarCollapsed == true) sidebar-collapse @endif ">
<div class="loading">
    <div class="loading-wheel fa fa-spin"></div>
</div>
<div id="app" class="wrapper">
    @include("layouts.includes.navbar")

    <div class="content-wrapper">
        <section class="content @yield('sectionClass')">
            @yield('content')
        </section>
    </div>

    @yield('aside-right')
</div>
{{-- Polyfill for edge and IE --}}
<script src="https://cdn.jsdelivr.net/npm/formdata-polyfill@3.0.18/formdata.min.js"></script>
@include("layouts.includes.footer")
@yield('footer')
</body>
</html>
