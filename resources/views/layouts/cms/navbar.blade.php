<!-- Left navbar links -->
<ul class="navbar-nav">
  <li class="nav-item">
    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
  </li>
  {{-- <li class="nav-item d-none d-sm-inline-block">
    <a href="index3.html" class="nav-link">Home</a>
  </li>
  <li class="nav-item d-none d-sm-inline-block">
    <a href="#" class="nav-link">Contact</a>
  </li> --}}
</ul>

<!-- Right navbar links -->
<ul class="navbar-nav ml-auto">
  <!-- Navbar Search -->
  {{-- <li class="nav-item">
    <a class="nav-link" data-widget="navbar-search" href="#" role="button">
      <i class="fas fa-search"></i>
    </a>
    <div class="navbar-search-block">
      <form class="form-inline">
        <div class="input-group input-group-sm">
          <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-navbar" type="submit">
              <i class="fas fa-search"></i>
            </button>
            <button class="btn btn-navbar" type="button" data-widget="navbar-search">
              <i class="fas fa-times"></i>
            </button>
          </div>
        </div>
      </form>
    </div>
  </li> --}}
  
  <!-- Notifications Dropdown Menu -->
  <li class="nav-item">
    <a class="nav-link" data-toggle="dropdown" href="#" aria-expanded="false" role="button">
      <i class="fa fa-user-circle"></i>
      <span class="badge badge-danger navbar-badge"></span>
    </a>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
      <a href="#" class="dropdown-item">
        <!-- Message Start -->
        <div class="media">
          <img src="{{ asset('dist/img/user.png') }}" class="img-size-50 mr-3 img-circle" alt="User Image">
          
          <div class="media-body">
            <h3 class="dropdown-item-title">
              {{Auth::user('web')->name}}
              
              <span class="float-right text-sm text-danger">
                
              </span>
            </h3>
            <p class="text-sm text-muted">{{Auth::user('web')->email}}</p>
            <p class="text-sm text-muted"></p>
            
          </div>
        </div>
      </a>
      <div>
        <div style="margin: 5px;">
          <div class="d-flex justify-content-end">
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn btn-sm btn-light btn-flat pull-right"> <i class="fa fa-sign-out"></i>
              Logout              
            </a>
          </div>
          <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
            @csrf
          </form>
        </div>
        <div class="clear"></div>
      </div>
      <!-- Message End -->
    </div>
  </li>
  {{-- <li class="nav-item">
    <a class="nav-link" data-widget="fullscreen" href="#" role="button">
      <i class="fas fa-expand-arrows-alt"></i>
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">
      <i class="fas fa-th-large"></i>
    </a>
  </li> --}}
</ul>