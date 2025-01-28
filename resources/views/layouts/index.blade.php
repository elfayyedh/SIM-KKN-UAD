@include('layouts.head-main')

<head>
    <title>@yield('title')</title>
    @include('layouts.head')
    @include('layouts.head-style')
    @yield('styles')
    <script>
        // Menjalankan script ini secepat mungkin untuk mengatur tema
        (function() {
            const savedMode = localStorage.getItem("theme") || "light";
            document.documentElement.setAttribute("data-bs-theme", savedMode);
        })();
    </script>
</head>

@include('layouts.body')

<div class="layout-wrapper">
    @include('layouts.menu')
    <div class="main-content">
        @yield('content')
        @include('layouts.footer')
    </div>
</div>

{{-- /Right sidebar --}}

@include('layouts.vendor-scripts')
@yield('pageScript')
<script src="{{ asset('assets/js/app.js') }}"></script>

</body>

</html>
