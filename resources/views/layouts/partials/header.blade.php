<header class="navbar pcoded-header navbar-expand-lg navbar-light">
    <div class="m-header">
        <a class="mobile-menu" id="mobile-collapse1" href="javascript:void(0)"><span></span></a>
        <a href="{{ route('home') }}" class="b-brand">
            <div class="b-bg">
                <i class="feather icon-trending-up"></i>
            </div>
            <span class="b-title">{{ config('app.name', 'Sherlock') }}</span>
        </a>
    </div>
    <a class="mobile-menu" id="mobile-header" href="javascript:void(0)">
        <i class="feather icon-more-horizontal"></i>
    </a>
    <div class="collapse navbar-collapse">
        <ul class="navbar-nav me-auto">
            <li class="nav-item">
                <div class="main-search">
                    <div class="input-group">
                        <input type="text" id="m-search" class="form-control" placeholder="Search . . .">
                        <a href="javascript:void(0)" class="input-group-append search-close">
                            <i class="feather icon-x input-group-text"></i>
                        </a>
                        <span class="input-group-append search-btn btn btn-primary">
                            <i class="feather icon-search input-group-text"></i>
                        </span>
                    </div>
                </div>
            </li>
        </ul>
        <ul class="navbar-nav ms-auto">
            <li>
                <div class="dropdown">
                    <a class="dropdown-toggle" href="javascript:void(0)" data-bs-toggle="dropdown"><i class="icon feather icon-bell"></i></a>
                    <div class="dropdown-menu dropdown-menu-end notification">
                        <div class="noti-head">
                            <h6 class="d-inline-block m-b-0">Notifications</h6>
                            <div class="float-end">
                                <a href="javascript:void(0)" class="m-r-10">mark as read</a>
                                <a href="javascript:void(0)">clear all</a>
                            </div>
                        </div>
                        <ul class="noti-body">
                            <li class="n-title">
                                <p class="m-b-0">NEW</p>
                            </li>
                            <li class="notification">
                                <div class="media">
                                    <img class="img-radius" src="{{ asset('assets/images/user/avatar-1.jpg') }}" alt="Generic placeholder image">
                                    <div class="media-body">
                                        <p><strong>John Doe</strong><span class="n-time text-muted"><i class="icon feather icon-clock m-r-10"></i>30 min</span></p>
                                        <p>New ticket Added</p>
                                    </div>
                                </div>
                            </li>
                            <li class="n-title">
                                <p class="m-b-0">EARLIER</p>
                            </li>
                            <li class="notification">
                                <div class="media">
                                    <img class="img-radius" src="{{ asset('assets/images/user/avatar-2.jpg') }}" alt="Generic placeholder image">
                                    <div class="media-body">
                                        <p><strong>Joseph William</strong><span class="n-time text-muted"><i class="icon feather icon-clock m-r-10"></i>30 min</span></p>
                                        <p>Prchace New Theme and make payment</p>
                                    </div>
                                </div>
                            </li>
                            <li class="notification">
                                <div class="media">
                                    <img class="img-radius" src="{{ asset('assets/images/user/avatar-3.jpg') }}" alt="Generic placeholder image">
                                    <div class="media-body">
                                        <p><strong>Sara Soudein</strong><span class="n-time text-muted"><i class="icon feather icon-clock m-r-10"></i>30 min</span></p>
                                        <p>currently login</p>
                                    </div>
                                </div>
                            </li>
                        </ul>
                        <div class="noti-footer">
                            <a href="javascript:void(0)">show all</a>
                        </div>
                    </div>
                </div>
            </li>
            <li>
                <div class="dropdown drp-user">
                    <a href="javascript:void(0)" class="dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="icon feather icon-settings"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end profile-notification">
                        <div class="pro-head d-flex align-items-center">
                            @if(Auth::user()->profile_photo_type == 'upload' || Auth::user()->profile_photo_type == 'icon')
                            <img src="{{ Auth::user()->profile_photo_url }}" class="img-radius me-2" alt="User-Profile-Image" style="width: 40px; height: 40px;">
                        @else
                            <div class="rounded-circle text-white d-flex align-items-center justify-content-center me-2" style="background-color: {{ Auth::user()->profile_photo }}; width: 40px; height: 40px;">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                        @endif
                        
                            <span>{{ Auth::user()->name ?? 'John Doe' }}</span>
                            <a href="{{ route('logout') }}" class="dud-logout ms-auto" title="Logout"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="feather icon-log-out"></i>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                        <ul class="pro-body">
                            <li><a href="{{ route('settings.index') }}" class="dropdown-item"><i class="feather icon-settings"></i> Settings</a></li>
                            <li class="dropdown-item theme-toggle">
                                <div class="button"></div>
                                <svg class="cat" data-name="Layer 1" height="50" id="Layer_1" viewBox="0 0 24 24" width="50" xmlns="http://www.w3.org/2000/svg"><title/><path d="M16,7,13,9V5.2071a.5.5,0,0,1,.8536-.3535Z" style="fill:#ad514e"/><path d="M18,7l3,2V5.2071a.5.5,0,0,0-.8536-.3535Z" style="fill:#ad514e"/><path d="M6,13H4V5A1,1,0,0,1,5,4H5A1,1,0,0,1,6,5Z" style="fill:#4b5661"/><path d="M5.5,22h0A1.5,1.5,0,0,1,4,20.5V13H7v7.5A1.5,1.5,0,0,1,5.5,22Z" style="fill:#4b5661"/><path d="M15.5,22h0A1.5,1.5,0,0,1,14,20.5V13h3v7.5A1.5,1.5,0,0,1,15.5,22Z" style="fill:#4b5661"/><path d="M17,19c-3-4-10-4-13,0V12c3-4,10-4,13,0Z" style="fill:#4b5661"/><path d="M17,14h0a4,4,0,0,1-4-4V7h8v3A4.00005,4.00005,0,0,1,17,14Z" style="fill:#4b5661"/><circle cx="15.25" cy="9.75" r="0.75" style="fill:#fff"/><circle cx="19" cy="9.75" r="0.75" style="fill:#fff"/></svg>
                            </li>
                        </ul>
                    </div>
                </div>
            </li>
            
            
            

            
            
        </ul>
    </div>
</header>

