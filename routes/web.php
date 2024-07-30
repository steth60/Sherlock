<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\MfaController;
use App\Http\Controllers\Instance\{
    DashboardController, 
    InstanceController, 
    ScheduleController
};
use App\Http\Controllers\{
    SettingsController, 
    //MenuController, 
    PasswordChangeController,
    TicketController,
    CacheDebugController,
};
use App\Http\Controllers\Admin\{
    UserController, 
    PermissionController, 
    GroupController, 
    AdminDashboardController,
    MenuController,
    LaraUpdaterController,
};
use Laravel\Fortify\Http\Controllers\{
    AuthenticatedSessionController,
    RegisteredUserController,
    PasswordResetLinkController,
    NewPasswordController
};
use Laravel\Fortify\Features;
use Laravel\Fortify\RoutePath;

// Fortify Routes
Route::group(['middleware' => config('fortify.middleware', ['web', 'checkMaintainerMode'])], function () {
    $enableViews = config('fortify.views', true);

    // Authentication
    if ($enableViews) {
        Route::get(RoutePath::for('login', '/login'), [AuthenticatedSessionController::class, 'create'])
            ->middleware(['guest:' . config('fortify.guard')])
            ->name('login');

        Route::post(RoutePath::for('login', '/login'), [AuthenticatedSessionController::class, 'store'])
            ->middleware(['guest:' . config('fortify.guard')]);

        Route::post(RoutePath::for('logout', '/logout'), [AuthenticatedSessionController::class, 'destroy'])
            ->name('logout');
    }

    // Registration
    if (Features::enabled(Features::registration())) {
        if ($enableViews) {
            Route::get(RoutePath::for('register', '/register'), [RegisteredUserController::class, 'create'])
                ->middleware(['guest:' . config('fortify.guard')])
                ->name('register');
        }

        Route::post(RoutePath::for('register', '/register'), [RegisteredUserController::class, 'store'])
            ->middleware(['guest:' . config('fortify.guard')]);
    }

    // Password Reset
    if (Features::enabled(Features::resetPasswords())) {
        Route::get(RoutePath::for('password.request', '/forgot-password'), [PasswordResetLinkController::class, 'create'])
            ->middleware(['guest:' . config('fortify.guard')])
            ->name('password.request');

        Route::get(RoutePath::for('password.reset', '/reset-password/{token}'), [NewPasswordController::class, 'create'])
            ->middleware(['guest:' . config('fortify.guard')])
            ->name('password.reset');

        Route::post(RoutePath::for('password.email', '/forgot-password'), [PasswordResetLinkController::class, 'store'])
            ->middleware(['guest:' . config('fortify.guard')])
            ->name('password.email');

        Route::post(RoutePath::for('password.update', '/reset-password'), [NewPasswordController::class, 'store'])
            ->middleware(['guest:' . config('fortify.guard')])
            ->name('password.update');
    }

  });


// MFA Setup Routes (should be protected)
Route::group(['middleware' => config('fortify.middleware', ['web', 'auth'])], function () {
    Route::get('two-factor/setup', [MfaController::class, 'showSetupForm'])->name('two-factor.setup');
    Route::prefix('two-factor')->name('two-factor.')->group(function () {
        Route::get('setup/totp', [MfaController::class, 'showTotpSetupForm'])->name('setup.totp');
        Route::get('setup/email', [MfaController::class, 'showEmailSetupForm'])->name('setup.email');
        Route::post('setup', [MfaController::class, 'setupMfa'])->name('setup.post');
    });
});

// MFA Challenge Routes (should be accessible during authentication)
Route::group(['middleware' => ['web']], function () {
    Route::prefix('two-factor')->name('two-factor.')->group(function () {
        Route::get('challenge/totp', [MfaController::class, 'showChallenge'])->name('challenge.totp');
        Route::post('challenge/totp/verify', [MfaController::class, 'verifyChallenge'])->name('challenge.totp.verify');
        
        Route::post('challenge/email/send', [MfaController::class, 'sendEmailMfaCode'])->name('challenge.email.send');
        Route::get('challenge/email', [MfaController::class, 'showEmailChallenge'])->name('challenge.email');
        Route::post('challenge/email/verify', [MfaController::class, 'verifyEmailMfaCode'])->name('challenge.email.verify');
    });
});

Route::get('/user/two-factor-recovery-codes', [MfaController::class, 'showRecoveryCodes'])->name('two-factor.recovery-codes');


Route::get('/', function () {
    return auth()->check() ? redirect('/home') : redirect('/login');
});

Route::middleware(['auth', 'verified', 'mfa'])->group(function () {
    Route::get('/home', function () {return view('home');})->name('home');
    Route::get('/instances', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/menu', [MenuController::class, 'getMenuItems'])->name('menu');

    // Instance Routes
    Route::middleware('permission:manage_instances')->group(function () {
        Route::prefix('instances')->name('instances.')->group(function () {
            Route::get('/', [InstanceController::class, 'index'])->name('index');
            Route::get('/create', [InstanceController::class, 'create'])->name('create');
            Route::post('/', [InstanceController::class, 'store'])->name('store');
            Route::get('/{instance}', [InstanceController::class, 'show'])->name('show');
            Route::get('/{instance}/edit', [InstanceController::class, 'edit'])->name('edit');
            Route::put('/{instance}', [InstanceController::class, 'update'])->name('update');
            Route::delete('/{instance}', [InstanceController::class, 'delete'])->name('destroy');
            Route::get('running', [InstanceController::class, 'running'])->name('running');
            Route::post('{instance}/start', [InstanceController::class, 'start'])->name('start');
            Route::post('{instance}/stop', [InstanceController::class, 'stop'])->name('stop');
            Route::post('{instance}/restart', [InstanceController::class, 'restart'])->name('restart');
            Route::post('{instance}/delete', [InstanceController::class, 'delete'])->name('delete');
            Route::get('{instance}/output', [InstanceController::class, 'output'])->name('output');
            Route::get('{instance}/update', [InstanceController::class, 'showUpdatePage'])->name('update.page');
            Route::post('{instance}/check-updates', [InstanceController::class, 'checkUpdates'])->name('check.updates');
            Route::post('{instance}/confirm-updates', [InstanceController::class, 'confirmUpdates'])->name('confirm.updates');
            Route::post('/instances/{instance}/rollback', [InstanceController::class, 'rollback'])->name('instances.rollback');
            Route::get('{instance}/status', [InstanceController::class, 'getStatus']);
            Route::get('{instance}/env', [InstanceController::class, 'getEnv'])->name('get.env');
            Route::post('{instance}/env', [InstanceController::class, 'updateEnv'])->name('update.env');
            Route::get('{instance}/metrics', [InstanceController::class, 'getMetrics']);
            Route::post('{instance}/notes', [InstanceController::class, 'storeNote'])->name('notes.store');
            Route::get('{instance}/notes', [InstanceController::class, 'getNotes'])->name('notes.index');
            Route::delete('{instance}/notes/{note}', [InstanceController::class, 'destroyNote'])->name('notes.destroy');
            Route::get('{instance}/schedules/create', [ScheduleController::class, 'create'])->name('schedules.create');
            Route::post('{instance}/schedules', [ScheduleController::class, 'store'])->name('schedules.store');
            Route::prefix('{instance}/files')->name('files.')->group(function () {
                Route::get('/', [InstanceController::class, 'listFiles'])->name('index');
                Route::get('view', [InstanceController::class, 'viewFile'])->name('view');
                Route::post('update', [InstanceController::class, 'updateFile'])->name('update');
                Route::get('editor', [InstanceController::class, 'fileEditor'])->name('editor');
            });
            Route::post('/schedules/{schedule}/trigger-now', [ScheduleController::class, 'triggerNow']);

        });
        Route::get('/holiday', [App\Http\Controllers\HolidayController::class, 'index'])->name('holiday.index');
        Route::get('/holiday/calendar', [App\Http\Controllers\HolidayController::class, 'calendar'])->name('holiday.calendar');
        // Schedule Routes
        Route::prefix('schedules')->name('schedules.')->group(function () {
            Route::get('create', [ScheduleController::class, 'create'])->name('create');
            Route::post('/', [ScheduleController::class, 'store'])->name('store');
            Route::get('{schedule}/edit', [ScheduleController::class, 'edit'])->name('edit');
            Route::put('{schedule}', [ScheduleController::class, 'update'])->name('update');
            Route::delete('{schedule}', [ScheduleController::class, 'destroy'])->name('destroy');
            
        });
    });
    
    
    // Settings Routes

Route::middleware('permission:manage_settings')->group(function () {
    Route::prefix('settings')->name('settings.')->group(function () {
        // Main Settings Page
        Route::get('/', [SettingsController::class, 'index'])->name('index');

        // Personal Information
        Route::get('personal-info', [SettingsController::class, 'showPersonalInfoForm'])->name('personal-info');
        Route::post('personal-info', [SettingsController::class, 'updatePersonalInfo'])->name('personal-info.update');

        // Email Settings
        Route::get('change-email', [SettingsController::class, 'showChangeEmailForm'])->name('change-email');
        Route::post('change-email', [SettingsController::class, 'changeEmail'])->name('change-email.update');

        // Password Settings
        Route::get('change-password', [SettingsController::class, 'showChangePasswordForm'])->name('change-password');
        Route::post('change-password', [SettingsController::class, 'changePassword'])->name('change-password.update');

        // MFA Settings
        Route::get('mfa', [SettingsController::class, 'showMfaSettings'])->name('mfa');
        Route::post('mfa/update', [SettingsController::class, 'updateMfaSettings'])->name('mfa.update');
        Route::post('mfa/add', [SettingsController::class, 'addMfaMethod'])->name('mfa.add');
        Route::post('mfa/remove', [SettingsController::class, 'removeMfaMethod'])->name('mfa.remove');
        Route::post('mfa/regenerate', [SettingsController::class, 'regenerateBackupCodes'])->name('mfa.regenerate');
        Route::get('mfa-reset', [SettingsController::class, 'showMfaResetForm'])->name('mfa-reset');
        Route::post('mfa-reset', [SettingsController::class, 'resetMfa'])->name('mfa-reset.update');

        // Account Recovery
        Route::get('account-recovery', [SettingsController::class, 'showAccountRecoveryForm'])->name('account-recovery');
        Route::post('account-recovery', [SettingsController::class, 'updateAccountRecovery'])->name('account-recovery.update');
        Route::post('account-recovery/update', [SettingsController::class, 'updateAccountRecoveryOptions'])->name('account-recovery.update-options');
        
        // Account Information
        Route::get('account-info', [SettingsController::class, 'showAccountInfoForm'])->name('account-info');
        Route::post('account-info', [SettingsController::class, 'updateAccountInfo'])->name('account-info.update');

        // Login Notifications
        Route::post('login-notifications', [SettingsController::class, 'updateLoginNotifications'])->name('login-notifications.update');

        // Trusted Devices
        Route::post('trusted-devices', [SettingsController::class, 'storeTrustedDevice'])->name('trusted-devices.store');
        Route::put('trusted-devices/{id}', [SettingsController::class, 'updateTrustedDevice'])->name('trusted-devices.update');
        Route::delete('trusted-devices/{id}', [SettingsController::class, 'destroyTrustedDevice'])->name('trusted-devices.destroy');
        Route::post('trusted-devices/{id}/renew', [SettingsController::class, 'renewTrustedDevice'])->name('trusted-devices.renew');

        // Active Sessions
        Route::get('active-sessions', [SettingsController::class, 'getActiveSessions'])->name('active-sessions');
        Route::post('active-sessions/logout/{session_id}', [SettingsController::class, 'logoutSession'])->name('active-sessions.logout');

        // Profile Photos
        Route::get('profile-photo', [SettingsController::class, 'showProfilePhotoForm'])->name('profile-photo');
        Route::post('profile-photo', [SettingsController::class, 'updateProfilePhoto'])->name('profile-photo.update');

        // Theme Settings
        Route::post('theme', [SettingsController::class, 'updateTheme'])->name('theme.update');
    });
});

    
    
        // Theme Settings
        Route::post('settings/theme', [SettingsController::class, 'updateTheme'])->name('settings.updateTheme');

        // Admin Routes
        Route::middleware('permission:admin_access')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/unverified-email-users', [AdminDashboardController::class, 'unverifiedEmailUsers'])->name('unverified-email-users');
        Route::get('/mfa-not-enabled-users', [AdminDashboardController::class, 'mfaNotEnabledUsers'])->name('mfa-not-enabled-users');

        // Permissions
        Route::resource('permissions', PermissionController::class)->only(['index', 'store', 'destroy']);
        Route::post('{permission}/rename', [PermissionController::class, 'rename'])->name('permissions.rename');
        Route::get('{permission}/children', [PermissionController::class, 'getChildren']);
        // Groups
        Route::resource('groups', GroupController::class)->only(['index', 'store', 'destroy']);
        Route::post('{group}/permissions', [GroupController::class, 'assignPermissions'])->name('groups.assignPermissions');
        Route::post('{group}/groups', [GroupController::class, 'assignGroups'])->name('groups.assignGroups');
        Route::post('{group}/rename', [GroupController::class, 'rename'])->name('groups.rename');
        Route::post('reorder', [GroupController::class, 'reorder'])->name('groups.reorder');

        // Users
        Route::resource('users', UserController::class)->only(['index', 'show', 'update']);
        Route::post('{user}/set-temp-password', [UserController::class, 'setTempPassword'])->name('users.setTempPassword');
        Route::post('{user}/remove-mfa', [UserController::class, 'removeMfa'])->name('users.removeMfa');
        Route::post('{user}/assign-groups', [UserController::class, 'assignGroups'])->name('users.assignGroups');
        Route::post('{user}/deauth-device/{device}', [UserController::class, 'deauthDevice'])->name('users.deauthDevice');
        Route::post('{user}/disable', [UserController::class, 'disableUser'])->name('users.disableUser');

        // Navigation Menu
        Route::get('nav', [MenuController::class, 'index'])->name('nav.index');
        Route::post('nav', [MenuController::class, 'store'])->name('nav.store');
        Route::put('{menuItem}', [MenuController::class, 'update'])->name('nav.update');
        Route::delete('{menuItem}', [MenuController::class, 'destroy'])->name('nav.destroy');
        Route::post('reorder', [MenuController::class, 'reorder'])->name('menu.reorder');


        Route::post('/invite', [AdminDashboardController::class, 'invite'])->name('invite');
        Route::post('/resend-invite/{id}', [AdminDashboardController::class, 'resendInvite'])->name('resend-invite');
        Route::post('/revoke-invite/{id}', [AdminDashboardController::class, 'revokeInvite'])->name('revoke-invite');
        Route::post('/toggle-maintainer-mode', [AdminDashboardController::class, 'toggleMaintainerMode'])->name('toggle-maintainer-mode');

        Route::prefix('update')->name('update.')->group(function () {
            Route::get('/', [LaraUpdaterController::class, 'index'])->name('index');
            Route::get('/check', [LaraUpdaterController::class, 'check'])->name('check');
            Route::get('/install', [LaraUpdaterController::class, 'install'])->name('install');
            Route::get('/logs', [LaraUpdaterController::class, 'logs'])->name('logs');
        });
    
    });
    

    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::post('/tickets/saveFilter', [TicketController::class, 'saveFilter'])->name('tickets.saveFilter');
    Route::get('/tickets/more', [TicketController::class, 'loadMore'])->name('tickets.more');;
    Route::get('/debug-cache', [CacheDebugController::class, 'index']);
});


Route::get('/user/password-change', function () {
    return view('auth.change-password');
})->middleware(['auth'])->name('password.change');

Route::post('/user/password-change', [PasswordChangeController::class, 'update'])
    ->middleware(['auth'])
    ->name('password.change.update');

Route::get('/unauthorized', function () {
    return view('errors.403');
})->name('unauthorized');

// Logout Route
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');
