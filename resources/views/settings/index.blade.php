@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    Account Settings
                </div>
                <div class="list-group list-group-flush">
                    <a href="#personal-info" class="list-group-item list-group-item-action active" data-toggle="list">Personal Info</a>
                    <a href="#email" class="list-group-item list-group-item-action" data-toggle="list">Email</a>
                    <a href="#password" class="list-group-item list-group-item-action" data-toggle="list">Password</a>
                    <a href="#mfa" class="list-group-item list-group-item-action" data-toggle="list">Two-Factor Authentication</a>
                    <a href="#recovery" class="list-group-item list-group-item-action" data-toggle="list">Account Recovery</a>
                    <a href="#trusted-devices" class="list-group-item list-group-item-action" data-toggle="list">Trusted Devices</a>
                    <a href="#mfa-reset" class="list-group-item list-group-item-action" data-toggle="list">MFA Token Reset</a>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="tab-content">
                <div class="tab-pane fade show active" id="personal-info">
                    @include('settings.personal-info')
                </div>
                <div class="tab-pane fade" id="email">
                    @include('settings.change-email')
                </div>
                <div class="tab-pane fade" id="password">
                    @include('settings.change-password')
                </div>
                <div class="tab-pane fade" id="mfa">
                    @include('settings.mfa')
                </div>
                <div class="tab-pane fade" id="recovery">
                    @include('settings.account-recovery')
                </div>
                <div class="tab-pane fade" id="mfa-reset">
                    @include('settings.mfa-reset')
                </div>
                <div class="tab-pane fade" id="trusted-devices">
                    @include('settings.trusted-devices')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('a[data-toggle="list"]').on('shown.bs.tab', function (e) {
            localStorage.setItem('lastTab', $(this).attr('href'));
        });
        var lastTab = localStorage.getItem('lastTab');
        if (lastTab) {
            $('[href="' + lastTab + '"]').tab('show');
        }
    });
</script>
@endsection