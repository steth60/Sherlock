<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MfaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InstanceController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SettingsController;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Laravel\Fortify\Http\Controllers\RegisteredUserController;
use Laravel\Fortify\Http\Controllers\PasswordResetLinkController;
use Laravel\Fortify\Http\Controllers\NewPasswordController;
use Laravel\Fortify\Http\Controllers\TwoFactorAuthenticatedSessionController;
use Laravel\Fortify\Features;
use Laravel\Fortify\RoutePath;

// Fortify Routes
Route::group(['middleware' => config('fortify.middleware', ['web'])], function () {
    $enableViews = config('fortify.views', true);

    // Authentication...
    if ($enableViews) {
        Route::get(RoutePath::for('login', '/login'), [AuthenticatedSessionController::class, 'create'])
            ->middleware(['guest:' . config('fortify.guard')])
            ->name('login');

        Route::post(RoutePath::for('login', '/login'), [AuthenticatedSessionController::class, 'store'])
            ->middleware(['guest:' . config('fortify.guard')]);

        Route::post(RoutePath::for('logout', '/logout'), [AuthenticatedSessionController::class, 'destroy'])
            ->name('logout');
    }

    // Registration...
    if (Features::enabled(Features::registration())) {
        if ($enableViews) {
            Route::get(RoutePath::for('register', '/register'), [RegisteredUserController::class, 'create'])
                ->middleware(['guest:' . config('fortify.guard')])
                ->name('register');
        }

        Route::post(RoutePath::for('register', '/register'), [RegisteredUserController::class, 'store'])
            ->middleware(['guest:' . config('fortify.guard')]);
    }

    // Password Reset...
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
    Route::get('/two-factor-setup', [MfaController::class, 'showSetupForm'])->name('two-factor.setup')->middleware('auth');
    Route::post('/two-factor-setup', [MfaController::class, 'setupMfa'])->name('two-factor.setup.post');

    Route::get('/two-factor-challenge', [MfaController::class, 'showChallenge'])
        ->name('two-factor.challenge')
        ->middleware(['auth:' . config('fortify.guard')]);

    Route::post('/two-factor-challenge', [MfaController::class, 'verifyChallenge'])
        ->name('two-factor.challenge.verify')
        ->middleware(['auth:' . config('fortify.guard')]);

    // Custom Two-Factor Recovery Codes Route (renamed to avoid conflict with Fortify)
    Route::get('/two-factor-recovery-codes', [MfaController::class, 'showRecoveryCodes'])
        ->name('custom.two-factor.recovery-codes')
        ->middleware(['auth:' . config('fortify.guard')]);
});

Route::get('/', function () {
    if (auth()->check()) {
        return redirect('/home');
    }
    return redirect('/login');
});

Route::middleware(['auth', 'verified', 'mfa'])->group(function () {
    Route::get('/home', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('instances', InstanceController::class);
    Route::get('/instances', [InstanceController::class, 'index'])->name('instances.index');
    Route::get('/instances/running', [InstanceController::class, 'running'])->name('instances.running');
    Route::get('/instances/create', [InstanceController::class, 'create'])->name('instances.create');
    Route::post('/instances', [InstanceController::class, 'store'])->name('instances.store');
    Route::get('/instances/{instance}', [InstanceController::class, 'show'])->name('instances.show');
    Route::post('/instances/{instance}/start', [InstanceController::class, 'start'])->name('instances.start');
    Route::post('/instances/{instance}/stop', [InstanceController::class, 'stop'])->name('instances.stop');
    Route::post('/instances/{instance}/restart', [InstanceController::class, 'restart'])->name('instances.restart');
    Route::post('/instances/{instance}/delete', [InstanceController::class, 'delete'])->name('instances.delete');
    Route::get('/instances/{instance}/output', [InstanceController::class, 'output'])->name('instances.output');
    Route::get('/instances/{instance}/update', [InstanceController::class, 'showUpdatePage'])->name('instances.update.page');
    Route::post('/instances/{instance}/check-updates', [InstanceController::class, 'checkUpdates'])->name('instances.check.updates');
    Route::post('/instances/{instance}/confirm-updates', [InstanceController::class, 'confirmUpdates'])->name('instances.confirm.updates');
    Route::get('/instances/{instance}/edit', [InstanceController::class, 'edit'])->name('instances.edit');
    Route::post('/instances/{instance}', [InstanceController::class, 'update'])->name('instances.update');
    Route::get('/instances/{instance}/status', [InstanceController::class, 'getStatus']);
    Route::get('/instances/{instance}/env', [InstanceController::class, 'getEnv'])->name('instances.get.env');
    Route::post('/instances/{instance}/env', [InstanceController::class, 'updateEnv'])->name('instances.update.env');
    Route::get('instances/{instance}/schedules/create', [ScheduleController::class, 'create'])->name('schedules.create');
    Route::post('instances/{instance}/schedules', [ScheduleController::class, 'store'])->name('schedules.store');
    Route::get('/instances/{instance}/metrics', [InstanceController::class, 'getMetrics']);
    Route::post('/instances/{instance}/notes', [InstanceController::class, 'storeNote'])->name('instances.notes.store');
    Route::delete('/notes/{note}', [InstanceController::class, 'destroyNote'])->name('notes.destroy');
    Route::get('/instances/{instance}/notes', [InstanceController::class, 'getNotes'])->name('instances.notes.index');
    Route::get('/instances/{instance}/files', [InstanceController::class, 'listFiles'])->name('instances.files');
Route::get('/instances/{instance}/files/view', [InstanceController::class, 'viewFile'])->name('instances.files.view');
Route::post('/instances/{instance}/files/update', [InstanceController::class, 'updateFile'])->name('instances.files.update');
Route::get('/instances/{instance}/files/editor', [InstanceController::class, 'fileEditor'])->name('instances.files.editor');
    Route::get('schedules/{schedule}/edit', [ScheduleController::class, 'edit'])->name('schedules.edit');
    Route::put('/schedules/{schedule}', [ScheduleController::class, 'update'])->name('schedules.update');
    Route::delete('/schedules/{schedule}', [ScheduleController::class, 'destroy'])->name('schedules.destroy');
    Route::post('schedules/{schedule}/trigger-now', [ScheduleController::class, 'triggerNow'])->name('schedules.triggerNow');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');

    // Personal Info
    Route::get('/settings/personal-info', [SettingsController::class, 'showPersonalInfoForm'])->name('settings.personal-info');
    Route::post('/settings/personal-info', [SettingsController::class, 'updatePersonalInfo'])->name('settings.personal-info.update');

    // Change Email
    Route::get('/settings/change-email', [SettingsController::class, 'showChangeEmailForm'])->name('settings.change-email');
    Route::post('/settings/change-email', [SettingsController::class, 'changeEmail'])->name('settings.change-email.update');

    // Change Password
    Route::get('/settings/change-password', [SettingsController::class, 'showChangePasswordForm'])->name('settings.change-password');
    Route::post('/settings/change-password', [SettingsController::class, 'changePassword'])->name('settings.change-password.update');

    // Manage MFA Settings
    Route::get('/settings/mfa', [SettingsController::class, 'showMfaSettings'])->name('settings.mfa');
    Route::post('/settings/mfa/add', [SettingsController::class, 'addMfaMethod'])->name('settings.mfa.add');
    Route::post('/settings/mfa/remove', [SettingsController::class, 'removeMfaMethod'])->name('settings.mfa.remove');
    Route::post('/settings/mfa/regenerate', [SettingsController::class, 'regenerateBackupCodes'])->name('settings.mfa.regenerate');

    // Account Recovery Options
    Route::get('/settings/account-recovery', [SettingsController::class, 'showAccountRecoveryForm'])->name('settings.account-recovery');
    Route::post('/settings/account-recovery', [SettingsController::class, 'updateAccountRecovery'])->name('settings.account-recovery.update');

    // MFA Token Reset
    Route::get('/settings/mfa-reset', [SettingsController::class, 'showMfaResetForm'])->name('settings.mfa-reset');
    Route::post('/settings/mfa-reset', [SettingsController::class, 'resetMfa'])->name('settings.mfa-reset.update');

    // Trusted Devices
    Route::get('/settings/trusted-devices', [SettingsController::class, 'index'])->name('settings.trusted-devices.index');
    Route::post('/settings/trusted-devices', [SettingsController::class, 'store'])->name('settings.trusted-devices.store');
    Route::put('/settings/trusted-devices/{id}', [SettingsController::class, 'update'])->name('settings.trusted-devices.update');
    Route::post('/settings/trusted-devices/{id}/renew', [SettingsController::class, 'renew'])->name('settings.trusted-devices.renew');
    Route::delete('/settings/trusted-devices/{id}', [SettingsController::class, 'destroy'])->name('settings.trusted-devices.destroy');
});

// Logout Route
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');