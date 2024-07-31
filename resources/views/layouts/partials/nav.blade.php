@php
    use Illuminate\Support\Str;
@endphp

<nav class="pcoded-navbar menu-light brand-red icon-colored menupos-static  active-red">
    <div class="navbar-wrapper">
      <div class="navbar-brand header-logo">
        <a href="index.html" class="b-brand">
            <div class="b-bg">
                <i class="feather icon-trending-up"></i>
            </div>
            <span class="b-title">{{ config('app.name', 'Sherlock') }}</span>
        </a>
        <a class="mobile-menu" id="mobile-collapse" href="#!"><span></span></a>
    </div>
        <div class="navbar-content scroll-div">
          <ul class="nav pcoded-inner-navbar">
              <li class="nav-item pcoded-menu-caption">
                  <label>Navigation</label>
              </li>
              @if($menuItems->isNotEmpty())
              <ul class="nav pcoded-inner-navbar" id="sidebar-menu">
                  @foreach($menuItems as $menuItem)
                      @php
                          $url = Str::startsWith($menuItem->url, 'route:') 
                              ? route(Str::after($menuItem->url, 'route:')) 
                              : $menuItem->url;
                      @endphp
                      @if($menuItem->children->isEmpty())
                          <li class="nav-item" data-username="{{ Str::slug($menuItem->title) }}">
                              <a href="{{ $url }}" class="nav-link">
                                  @if($menuItem->icon)
                                      <span class="pcoded-micon"><i class="{{ $menuItem->icon }}"></i></span>
                                  @endif
                                  <span class="pcoded-mtext">{{ $menuItem->title }}</span>
                              </a>
                          </li>
                      @else
                          <li class="nav-item pcoded-hasmenu">
                              <a href="{{ $url }}" class="nav-link dropdown-toggle" data-toggle="collapse" data-target="#menu-{{ $menuItem->id }}" aria-expanded="false" aria-controls="menu-{{ $menuItem->id }}">
                                  @if($menuItem->icon)
                                      <span class="pcoded-micon"><i class="{{ $menuItem->icon }}"></i></span>
                                  @endif
                                  <span class="pcoded-mtext">{{ $menuItem->title }}</span>
                              </a>
                              <ul class="pcoded-submenu collapse" id="menu-{{ $menuItem->id }}" data-parent="#sidebar-menu">
                                  @foreach($menuItem->children as $child)
                                      @if(empty($child->permission) || auth()->user()->hasPermission($child->permission))
                                          @php
                                              $childUrl = Str::startsWith($child->url, 'route:') 
                                                  ? route(Str::after($child->url, 'route:')) 
                                                  : $child->url;
                                          @endphp
                                          <li>
                                              <a href="{{ $childUrl }}" class="nav-link">
                                                  <span class="pcoded-mtext">{{ $child->title }}</span>
                                              </a>
                                          </li>
                                      @endif
                                  @endforeach
                              </ul>
                          </li>
                      @endif
                  @endforeach
              </ul>
          @endif
            </div>
        </div>
    </div>
</nav>
