{{-- <li class="nav-item {{ (Route::is('home'))? 'menu-open' : '' }}">
    <a href="#" class="nav-link {{ (Route::is('home'))? 'active' : '' }}">
        <i class="nav-icon fas fa-tachometer-alt"></i>
        <p>
            Dashboard
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="#" class="nav-link active">
                <i class="far fa-circle nav-icon"></i>
                <p>Management</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Sales</p>
            </a>
        </li>
    </ul>
</li> --}}
@php
use App\Helpers\StudentMenuHelper;
$menuTitle = null;
$menuList = StudentMenuHelper::GetMenu();
@endphp

@foreach ($menuList as $menu)
@if ($menuTitle != $menu->root)
@php 
$menuTitle = $menu->root; 
@endphp
<li class="nav-header">{{ strtoupper($menuTitle) }}</li>
@endif
@if (count($menu->branch) === 0)
<li class="nav-item">
    <a href="{{ route($menu->route_name) }}" class="nav-link {{ (Route::is($menu->route_validate))? 'active' : '' }}">
        <i class="nav-icon {{ $menu->icon_class }}"></i>
        <p>
            {{ $menu->title }}
        </p>
    </a>
</li>
@else
<li class="nav-item {{ (Route::is($menu->route_validate))? 'menu-open' : '' }}">
    <a href="#" class="nav-link {{ (Route::is($menu->route_validate))? 'active' : '' }}">
        <i class="nav-icon {{ $menu->icon_class }}"></i>
        <p>
            {{ $menu->title }}
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        @foreach ($menu->branch as $branch)
        <li class="nav-item">
            <a href="{{ route($branch->route_name) }}" class="nav-link {{ (Route::is($branch->route_validate))? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>{{$branch->title}}</p>
            </a>
        </li>
        @endforeach
    </ul>
</li>
@endif
@endforeach
<li class="nav-header"></li>