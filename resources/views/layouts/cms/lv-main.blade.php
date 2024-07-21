@php
use App\Helpers\MenuHelper;
@endphp
<!DOCTYPE html>
<!--
  This is a starter template page. Use this page to start your new project from
  scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{config('app.name')}} | @yield('page-title', 'Home')</title>
  
  <link rel="icon" href="{{ asset('images/pages/logo-app-favicon.ico') }}" type="image/x-icon">
  @yield('top-css')
  @include('layouts.cms.css')
  @yield('css')
  <style>
    .gap-1 {
      gap: .25rem;
    }
    .gap-2 {
      gap: .5rem;
    }
    .custom-text-sm {
      font-size: .875rem !important;
    }
    .inside-modal-overlay {
      border-radius: .25rem;
      -ms-flex-align: center;
      align-items: center;
      background-color: rgba(255, 255, 255, .7);
      display: -ms-flexbox;
      display: flex;
      -ms-flex-pack: center;
      justify-content: center;
      z-index: 50;
    }
    
    .inside-modal-overlay.loading {
      height: 100%;
      left: 0;
      position: absolute;
      top: 0;
      width: 100%;
    }
  </style>
  <!-- Scripts -->
  {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
  @vite(['resources/js/app.js'])
</head>
<body class="sidebar-mini layout-fixed">
  <div class="wrapper">
    
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
      @include('layouts.cms.navbar')
    </nav>
    <!-- /.navbar -->
    
    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-light-teal elevation-4">
      <!-- Brand Logo -->
      <a href="{{ url('/') }}" class="brand-link">
        <img src="{{ asset('images/pages/logo-app-new.png') }}" alt="Brand Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">SP SISWA</span>
      </a>
      
      <!-- Sidebar -->
      <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
          <div class="image">
            <img src="{{ asset('dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2" alt="User Image">
          </div>
          <div class="info">
            <a href="#" class="d-block">{{Auth::guard('admin')->user()->name}}</a>
          </div>
        </div>
        
        <!-- Sidebar Menu -->
        <nav class="mt-2">
          <ul class="nav nav-pills nav-sidebar flex-column nav-flat" data-widget="treeview" role="menu" data-accordion="false">
            <!-- 
              Add icons to the links using the .nav-icon class 
              with font-awesome or any other icon font library 
            -->
            @include('layouts.cms.sidebar')
          </ul>
        </nav>
        <!-- /.sidebar-menu -->
      </div>
      <!-- /.sidebar -->
    </aside>
    
    <!-- Content Wrapper. Contains page content -->
    @php
    $currentMenu = MenuHelper::getMenuByName($menuName);
    @endphp
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <div class="content-header text-sm">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">{{ $currentMenu->title }}</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                @foreach ($currentMenu->breadcrumb as $breadcrumb)
                <li class="breadcrumb-item">
                  @if ($breadcrumb['has_link'])
                  <a href="{{ route($breadcrumb['route_name']) }}">{{$breadcrumb['name']}}</a>
                  @else
                  {{$breadcrumb['name']}}
                  @endif
                </li>    
                @endforeach
              </ol>
            </div><!-- /.col -->
          </div><!-- /.row -->
        </div><!-- /.container-fluid -->
      </div>
      <!-- /.content-header -->
      
      <!-- Main content -->
      <div class="content custom-text-sm">
        <div class="container-fluid">
          {{ $slot }}
        </div><!-- /.container-fluid -->
      </div>
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    
    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
      <!-- Control sidebar content goes here -->
      <div class="p-3">
        <h5>Title</h5>
        <p>Sidebar content</p>
      </div>
    </aside>
    <!-- /.control-sidebar -->
    
    <!-- Main Footer -->
    <footer class="main-footer">
      @include('layouts.cms.footer')
    </footer>
  </div>
  <!-- ./wrapper -->
  
  <!-- REQUIRED SCRIPTS -->
  @livewireScripts
  @yield('top-script')
  @include('layouts.cms.script')
  <script>
    const Toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000
    });
    const swalBsButtons = Swal.mixin({
      customClass: {
        confirmButton: 'btn btn-primary mx-1',
        cancelButton: 'btn btn-light mx-1'
      },
      buttonsStyling: false
    })
    const swalLoader = Swal.mixin({
      title: 'Processing...',
      showConfirmButton: false,
      showCancelButton: false,
      didOpen: () => {
        Swal.showLoading()
      }
    })
    
    window.addEventListener('notification:show', function(event) {
      if(event.detail.close_modal) {
        $(event.detail.target).modal('hide')
      }
      
      if(event.detail.close_loading) {
        swalLoader.close()
      }
      let config = {
        icon: event.detail.icon,
        title: event.detail.title
      }
      if(event.detail.icon == 'error') {
        console.log(event.detail.title)
      }
      Toast.fire(config)
    })
    
    window.addEventListener('datatables:refresh', function(event) {
      window[event.detail.target].search('')
      window[event.detail.target].ajax.reload()
    })
    window.addEventListener('component-modal:close', function(event) {
      $(event.detail.target).modal('hide')
    })
  </script>
  @yield('script')
</body>
</html>
