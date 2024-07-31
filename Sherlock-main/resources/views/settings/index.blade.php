@extends('layouts.app')
@section('title', 'Settings')
@section('content')
<div class="container">

    <div class="row">
        <!-- Profile Overview Start -->
        <div class="col-lg-4">
            <div class="card user-card user-card-1">
                <div class="card-body pb-0">
                    <div class="float-end">
                        <span class="badge badge-success">Pro</span>
                    </div>
                    <div class="media user-about-block align-items-center mt-0 mb-3">
                        <div class="position-relative d-inline-block">
                            @if(Auth::user()->profile_photo_type == 'upload' || Auth::user()->profile_photo_type == 'icon')
                                <img class="img-radius img-fluid wid-80" src="{{ Auth::user()->profile_photo_url }}" alt="User image">
                            @else
                                <div class="rounded-circle text-white d-flex align-items-center justify-content-center" style="background-color: {{ Auth::user()->profile_photo }}; width: 80px; height: 80px; font-size: 30px;">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div class="media-body ms-3">
                            <h6 class="mb-1">{{ auth()->user()->name }}</h6>
                            <p class="mb-0 text-muted">{{ Auth::user()->department ?? 'Your Department' }}</p>
                            <p class="mb-0 text-muted">{{ auth()->user()->email }}</p>
                        </div>
                    </div>
                </div>

                <div class="nav flex-column nav-pills list-group list-group-flush list-pills" id="settings-tab" role="tablist" aria-orientation="vertical">
                    <a class="nav-link list-group-item list-group-item-action active" id="personal-info-tab" data-bs-toggle="pill" href="#personal-info" role="tab" aria-controls="personal-info" aria-selected="true">
                        <span class="f-w-500"><i class="feather icon-file-text m-r-10 h5 "></i>Personal Information</span>
                        <span class="float-end"><i class="feather icon-chevron-right"></i></span>
                    </a>
                    <a class="nav-link list-group-item list-group-item-action" id="account-info-tab" data-bs-toggle="pill" href="#account-info" role="tab" aria-controls="account-info" aria-selected="false">
                        <span class="f-w-500"><i class="feather icon-book m-r-10 h5 "></i>Account Information</span>
                        <span class="float-end"><i class="feather icon-chevron-right"></i></span>
                    </a>
                    <a class="nav-link list-group-item list-group-item-action" id="security-tab" data-bs-toggle="pill" href="#security" role="tab" aria-controls="security" aria-selected="false">
                        <span class="f-w-500"><i class="feather icon-shield m-r-10 h5 "></i>Security</span>
                        <span class="float-end"><i class="feather icon-chevron-right"></i></span>
                    </a>
                    <a class="nav-link list-group-item list-group-item-action" id="theme-tab" data-bs-toggle="pill" href="#theme" role="tab" aria-controls="theme" aria-selected="false">
                        <span class="f-w-500"><i class="feather icon-settings m-r-10 h5 "></i>Theme</span>
                        <span class="float-end"><i class="feather icon-chevron-right"></i></span>
                    </a>
                    <a class="nav-link list-group-item list-group-item-action" id="profile-photo-tab" data-bs-toggle="pill" href="#profile-photo" role="tab" aria-controls="profile-photo" aria-selected="false">
                        <span class="f-w-500"><i class="feather icon-image m-r-10 h5 "></i>Profile Photo</span>
                        <span class="float-end"><i class="feather icon-chevron-right"></i></span>
                    </a>
                </div>
            </div>
        </div>
        <!-- Profile Overview End -->

        <!-- Settings Content Start -->
        <div class="col-lg-8">
            <div class="tab-content bg-transparent p-0 shadow-none" id="settings-tabContent">
                <div class="tab-pane fade show active" id="personal-info" role="tabpanel" aria-labelledby="personal-info-tab">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="feather icon-user text-c-blue wid-20"></i><span class="p-l-5">Personal Information</span></h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('settings.personal-info.update') }}">
                                @csrf
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ auth()->user()->name }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="location">Location</label>
                                    <input type="text" class="form-control" id="location" name="location" value="{{ auth()->user()->location }}">
                                </div>
                                <div class="form-group">
                                    <label for="bio">About Me</label>
                                    <textarea class="form-control" id="bio" name="bio">{{ auth()->user()->bio }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label for="current_password">Current Password</label>
                                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Update Personal Info</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="account-info" role="tabpanel" aria-labelledby="account-info-tab">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="feather icon-book text-c-blue wid-20"></i><span class="p-l-5">Account Information</span></h5>
                        </div>
                        <div class="card-body">
                            <h5 class="mb-4">General</h5>
                            <form method="POST" >
                                @csrf
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="form-label">Username <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="username" value="{{ auth()->user()->username }}">
                                            <small class="form-text text-muted">Your Profile URL: https://pc.com/{{ auth()->user()->username }}</small>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="form-label">Account Email <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control" name="email" value="{{ auth()->user()->email }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="form-label">Language</label>
                                            <select class="form-control" name="language">
                                                <option>Washington</option>
                                                <option>India</option>
                                                <option>Africa</option>
                                                <option>New York</option>
                                                <option>Malesia</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="form-label">Signin Using</label>
                                            <select class="form-control" name="signin_method">
                                                <option>Password</option>
                                                <option>Face Recognition</option>
                                                <option>Thumb Impression</option>
                                                <option>Key</option>
                                                <option>Pin</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Update Account Info</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="security" role="tabpanel" aria-labelledby="security-tab">
                    @include('settings.trusted-devices')
                </div>
                <div class="tab-pane fade" id="theme" role="tabpanel" aria-labelledby="theme-tab">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="feather icon-settings text-c-blue wid-20"></i><span class="p-l-5">Theme</span></h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('settings.updateTheme') }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label for="theme_preference">Theme Preference</label>
                                    <select name="theme_preference" id="theme_preference" class="form-control">
                                        <option value="system" {{ $theme_preference == 'system' ? 'selected' : '' }}>System Default</option>
                                        <option value="light" {{ $theme_preference == 'light' ? 'selected' : '' }}>Light</option>
                                        <option value="dark" {{ $theme_preference == 'dark' ? 'selected' : '' }}>Dark</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary mt-3">Save</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="profile-photo" role="tabpanel" aria-labelledby="profile-photo-tab">
                    @include('settings.profile-photo')
                </div>
            </div>
        </div>
        <!-- Settings Content End -->
    </div>
</div>
@endsection

@section('scripts')

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var tabTriggerList = [].slice.call(document.querySelectorAll('a[data-bs-toggle="pill"]'))
        tabTriggerList.forEach(function(tabTriggerEl) {
            tabTriggerEl.addEventListener('shown.bs.tab', function(event) {
                localStorage.setItem('lastTab', event.target.getAttribute('href'));
            });
        });

        var lastTab = localStorage.getItem('lastTab');
        if (lastTab) {
            var lastTabEl = document.querySelector('[href="' + lastTab + '"]');
            if (lastTabEl) {
                var tab = new bootstrap.Tab(lastTabEl);
                tab.show();
            }
        }
    });
</script>
@endsection
