<!doctype html>
<html>
<head>
    @include('layouts.head')
</head>
<body>
<div class="app-container app-theme-white fixed-sidebar fixed-header body-tabs-line">
    <div class="app-main">
        <div class="app-main__outer">
            <div class="app-main__inner">
                @yield('content')
            </div>
        </div>
    </div>
    <div class="app-wrapper-footer">
        <link href="{{ asset('/assets/datatables/css/dataTables.min.css') }}" rel="stylesheet"
      type="text/css"/>
<link href="{{ asset('/assets/datatables/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet"
      type="text/css"/>


<script src="{{ asset('/assets/datatables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('/assets/datatables/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('/assets/datatables/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('/assets/datatables/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('/assets/datatables/js/jszip.min.js') }}"></script>
<script src="{{ asset('/assets/datatables/js/pdfmake.min.js') }}"></script>
<script src="{{ asset('/assets/datatables/js/vfs_fonts.js') }}"></script>
<script src="{{ asset('/assets/datatables/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('/assets/datatables/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('/assets/datatables/js/buttons.colVis.min.js') }}"></script>
<script src="{{ asset('/assets/datatables/js/dataTables.select.min.js') }}"></script>

        @stack('script')
    </div>
</div>
</body>
</html>