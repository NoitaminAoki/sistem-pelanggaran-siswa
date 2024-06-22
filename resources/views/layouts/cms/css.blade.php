<!-- Google Font: Source Sans Pro -->
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
<!-- Font Awesome Icons -->
<link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
<!-- SweetAlert2 -->
<link rel="stylesheet" href="{{ asset('plugins/sweetalert2/sweetalert2.min.css') }}">
<!-- Toastr -->
<link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
<!-- Theme style -->
<link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">

<style>
    .btn-loading {
        position: relative;
        background-image: url('{{asset("dist/img/spinner-white.svg")}}');
        background-position: center;
        background-repeat: no-repeat;
        background-size: 30px;
        color: transparent !important;
        pointer-events: none;
    }
    [x-cloak] {
        display: none !important;
    }
    .d-none-std {
        display: none;
    }
</style>