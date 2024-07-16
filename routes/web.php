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
};
use App\Http\Controllers\Admin\{
    UserController, 
    PermissionController, 
    GroupController, 
    AdminDashboardController,
    MenuController,
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

    // MFA Setup Routes
   // Route::get('mfa/setup', [MfaController::class, 'showSetupForm'])->name('mfa.setup');
   // Route::post('mfa/setup', [MfaController::class, 'store'])->name('mfa.store');

    Route::prefix('two-factor')->name('two-factor.')->group(function () {
        Route::get('setup', [MfaController::class, 'showSetupForm'])->name('setup')->middleware('auth');
        Route::post('setup', [MfaController::class, 'setupMfa'])->name('setup.post');
        Route::get('challenge', [MfaController::class, 'showChallenge'])->name('challenge')->middleware('auth:' . config('fortify.guard'));
        Route::post('challenge', [MfaController::class, 'verifyChallenge'])->name('challenge.verify')->middleware('auth:' . config('fortify.guard'));
    });

    Route::get('/user/two-factor-recovery-codes', [MfaController::class, 'showRecoveryCodes'])->name('two-factor.recovery-codes')->middleware('auth');

    //Route::get('fido/setup', [FIDOController::class, 'create'])->name('fido.setup');
    //Route::post('fido/setup', [FIDOController::class, 'store'])->name('fido.store');
    //Route::post('fido/verify', [FIDOController::class, 'verify'])->name('fido.verify');

   // Route::get('email-token/setup', [EmailTokenController::class, 'create'])->name('email-token.setup');
   // Route::post('email-token/setup', [EmailTokenController::class, 'store'])->name('email-token.store');
    //Route::post('email-token/verify', [EmailTokenController::class, 'verify'])->name('email-token.verify');
    //Route::get('/user/two-factor-recovery-codes', [MfaController::class, 'showRecoveryCodes'])->name('two-factor.recovery-codes')->middleware('auth');



});

Route::get('/', function () {
    return auth()->check() ? redirect('/home') : redirect('/login');
});

Route::middleware(['auth', 'verified', 'mfa'])->group(function () {
    Route::get('/home', function () {return view('home');})->name('home');
    Route::get('/instances', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/menu', [MenuController::class, 'getMenuItems'])->name('menu');

    // Instance Routes
    Route::middleware('permission:manage_instances')->group(function () {
        Route::resource('instances', InstanceController::class);
        Route::prefix('instances')->name('instances.')->group(function () {
            Route::get('running', [InstanceController::class, 'running'])->name('running');
            Route::post('{instance}/start', [InstanceController::class, 'start'])->name('start');
            Route::post('{instance}/stop', [InstanceController::class, 'stop'])->name('stop');
            Route::post('{instance}/restart', [InstanceController::class, 'restart'])->name('restart');
            Route::post('{instance}/delete', [InstanceController::class, 'delete'])->name('delete');
            Route::get('{instance}/output', [InstanceController::class, 'output'])->name('output');
            Route::get('{instance}/update', [InstanceController::class, 'showUpdatePage'])->name('update.page');
            Route::post('{instance}/check-updates', [InstanceController::class, 'checkUpdates'])->name('check.updates');
            Route::post('{instance}/confirm-updates', [InstanceController::class, 'confirmUpdates'])->name('confirm.updates');
            Route::get('{instance}/status', [InstanceController::class, 'getStatus']);
            Route::get('{instance}/env', [InstanceController::class, 'getEnv'])->name('get.env');
            Route::post('{instance}/env', [InstanceController::class, 'updateEnv'])->name('update.env');
            Route::get('{instance}/metrics', [InstanceController::class, 'getMetrics']);
            Route::post('{instance}/notes', [InstanceController::class, 'storeNote'])->name('notes.store');
            Route::get('{instance}/notes', [InstanceController::class, 'getNotes'])->name('notes.index');
            Route::delete('/notes/{note}', [InstanceController::class, 'destroyNote'])->name('notes.destroy');
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
            Route::get('/', [SettingsController::class, 'index'])->name('index');
            Route::get('personal-info', [SettingsController::class, 'showPersonalInfoForm'])->name('personal-info');
            Route::post('personal-info', [SettingsController::class, 'updatePersonalInfo'])->name('personal-info.update');
            Route::get('change-email', [SettingsController::class, 'showChangeEmailForm'])->name('change-email');
            Route::post('change-email', [SettingsController::class, 'changeEmail'])->name('change-email.update');
            Route::get('change-password', [SettingsController::class, 'showChangePasswordForm'])->name('change-password');
            Route::post('change-password', [SettingsController::class, 'changePassword'])->name('change-password.update');
            Route::get('mfa', [SettingsController::class, 'showMfaSettings'])->name('mfa');
            Route::post('mfa/add', [SettingsController::class, 'addMfaMethod'])->name('mfa.add');
            Route::post('mfa/remove', [SettingsController::class, 'removeMfaMethod'])->name('mfa.remove');
            Route::post('mfa/regenerate', [SettingsController::class, 'regenerateBackupCodes'])->name('mfa.regenerate');
            Route::get('account-recovery', [SettingsController::class, 'showAccountRecoveryForm'])->name('account-recovery');
            Route::post('account-recovery', [SettingsController::class, 'updateAccountRecovery'])->name('account-recovery.update');
            Route::get('mfa-reset', [SettingsController::class, 'showMfaResetForm'])->name('mfa-reset');
            Route::post('mfa-reset', [SettingsController::class, 'resetMfa'])->name('mfa-reset.update');
            Route::resource('trusted-devices', SettingsController::class)->only(['index', 'store', 'update', 'destroy']);
            Route::post('trusted-devices/{id}/renew', [SettingsController::class, 'renew'])->name('trusted-devices.renew');
        });
    });


        // Admin Routes
        Route::middleware('permission:admin_access')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('unverified-email-users', [AdminDashboardController::class, 'unverifiedEmailUsers'])->name('unverified-email-users');
        Route::get('mfa-not-enabled-users', [AdminDashboardController::class, 'mfaNotEnabledUsers'])->name('mfa-not-enabled-users');

        // Permissions
        Route::resource('permissions', PermissionController::class)->only(['index', 'store', 'destroy']);
        Route::post('permissions/{permission}/rename', [PermissionController::class, 'rename'])->name('permissions.rename');

        // Groups
        Route::resource('groups', GroupController::class)->only(['index', 'store', 'destroy']);
        Route::post('groups/{group}/permissions', [GroupController::class, 'assignPermissions'])->name('groups.assignPermissions');
        Route::post('groups/{group}/groups', [GroupController::class, 'assignGroups'])->name('groups.assignGroups');
        Route::post('groups/{group}/rename', [GroupController::class, 'rename'])->name('groups.rename');
        Route::post('groups/reorder', [GroupController::class, 'reorder'])->name('groups.reorder');

        // Users
        Route::resource('users', UserController::class)->only(['index', 'show', 'update']);
        Route::post('users/{user}/set-temp-password', [UserController::class, 'setTempPassword'])->name('users.setTempPassword');
        Route::post('users/{user}/remove-mfa', [UserController::class, 'removeMfa'])->name('users.removeMfa');
        Route::post('users/{user}/assign-groups', [UserController::class, 'assignGroups'])->name('users.assignGroups');
        Route::post('users/{user}/deauth-device/{device}', [UserController::class, 'deauthDevice'])->name('users.deauthDevice');
        Route::post('users/{user}/disable', [UserController::class, 'disableUser'])->name('users.disableUser');

        // Navigation Menu
        Route::get('nav', [MenuController::class, 'index'])->name('nav.index');
        Route::post('nav', [MenuController::class, 'store'])->name('nav.store');
        Route::put('nav/{menuItem}', [MenuController::class, 'update'])->name('nav.update');
        Route::delete('nav/{menuItem}', [MenuController::class, 'destroy'])->name('nav.destroy');
        Route::post('nav/reorder', [MenuController::class, 'reorder'])->name('menu.reorder');


        Route::post('/invite', [AdminDashboardController::class, 'invite'])->name('invite');
        Route::post('/resend-invite/{id}', [AdminDashboardController::class, 'resendInvite'])->name('resend-invite');
        Route::post('/revoke-invite/{id}', [AdminDashboardController::class, 'revokeInvite'])->name('revoke-invite');
        Route::post('/toggle-maintainer-mode', [AdminDashboardController::class, 'toggleMaintainerMode'])->name('toggle-maintainer-mode');
    
    });
    

   
   
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
