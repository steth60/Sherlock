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
                <ul class="nav pcoded-inner-navbar" id="sidebar-menu">
                    @if(isset($menuItems))
                        @foreach($menuItems as $menuItem)
                            <li class="nav-item pcoded-hasmenu">
                                <a href="{{ $menuItem->url }}" class="nav-link" data-toggle="collapse" data-target="#{{ Str::slug($menuItem->title) }}" aria-expanded="false" aria-controls="{{ Str::slug($menuItem->title) }}">
                                    <span class="pcoded-micon"><i class="{{ $menuItem->icon }}"></i></span>
                                    <span class="pcoded-mtext">{{ $menuItem->title }}</span>
                                </a>
                                @if($menuItem->children->count())
                                    <ul class="pcoded-submenu collapse" id="{{ Str::slug($menuItem->title) }}" data-parent="#sidebar-menu">
                                        @foreach($menuItem->children as $subMenuItem)
                                            <li>
                                                <a href="{{ $subMenuItem->url }}" class="nav-link">
                                                    <span class="pcoded-mtext">{{ $subMenuItem->title }}</span>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                    @endif
                </ul>
            </div>
        </div>
    </div>
</nav>
