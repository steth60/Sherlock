@php
    use Illuminate\Support\Str;
@endphp

<aside class="left-sidebar bg-sidebar">
    <div id="sidebar" class="sidebar sidebar-with-footer">
        <div class="app-brand">
            <a href="http://192.168.0.243" title="Sherlock Dashboard">
                <svg class="brand-icon" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid" width="30" height="33" viewBox="0 0 30 33">
                    <g fill="none" fill-rule="evenodd">
                        <path class="logo-fill-blue" fill="#7DBCFF" d="M0 4v25l8 4V0zM22 4v25l8 4V0z"></path>
                        <path class="logo-fill-white" fill="#FFF" d="M11 4v25l8 4V0z"></path>
                    </g>
                </svg>
                <span class="brand-name text-truncate">Sherlock Dashboard</span>
            </a>
        </div>
        <div class="" data-simplebar="init" style="height: 100%;">
            <div class="simplebar-content" style="padding: 0px;">
                <ul class="nav sidebar-inner" id="sidebar-menu">
                    @if(isset($menuItems))
                        @foreach($menuItems as $menuItem)
                            <li class="has-sub">
                                <a class="sidenav-item-link" href="{{ $menuItem->url }}" data-toggle="collapse" data-target="#{{ Str::slug($menuItem->title) }}" aria-expanded="false" aria-controls="{{ Str::slug($menuItem->title) }}">
                                    <i class="{{ $menuItem->icon }}"></i>
                                    <span class="nav-text">{{ $menuItem->title }}</span> <b class="caret"></b>
                                </a>
                                @if($menuItem->children->count())
                                    <ul class="collapse" id="{{ Str::slug($menuItem->title) }}" data-parent="#sidebar-menu">
                                        <div class="sub-menu">
                                            @foreach($menuItem->children as $subMenuItem)
                                                <li>
                                                    <a class="sidenav-item-link" href="{{ $subMenuItem->url }}">
                                                        <span class="nav-text">{{ $subMenuItem->title }}</span>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </div>
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                    @endif
                </ul>
            </div>
        </div>
    </aside>
