<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Actions\CreateUserWithInvitation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Mail;
use App\Mail\LoginNotificationMail;
use App\Models\User;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Actions\AttemptToAuthenticate;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\LoginRateLimiter;
use Laravel\Fortify\Contracts\VerifyEmailViewResponse;
use Laravel\Fortify\Contracts\LoginViewResponse;
use Laravel\Fortify\Contracts\RegisterViewResponse;
use Laravel\Fortify\Contracts\RequestPasswordResetLinkViewResponse;
use Laravel\Fortify\Contracts\ResetPasswordViewResponse;
use Laravel\Fortify\Contracts\TwoFactorChallengeViewResponse;
use Laravel\Fortify\Contracts\ConfirmPasswordViewResponse;
use Laravel\Fortify\Features;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind the contracts to the implementations
        $this->app->singleton(VerifyEmailViewResponse::class, function () {
            return new class implements VerifyEmailViewResponse {
                public function toResponse($request)
                {
                    return view('auth.verify-email');
                }
            };
        });

        $this->app->singleton(LoginViewResponse::class, function () {
            return new class implements LoginViewResponse {
                public function toResponse($request)
                {   
                    
                    return view('auth.login');
                }
            };
        });

        $this->app->singleton(RegisterViewResponse::class, function () {
            return new class implements RegisterViewResponse {
                public function toResponse($request)
                {
                    return view('auth.register');
                }
            };
        });

        $this->app->singleton(RequestPasswordResetLinkViewResponse::class, function () {
            return new class implements RequestPasswordResetLinkViewResponse {
                public function toResponse($request)
                {
                    return view('auth.forgot-password');
                }
            };
        });

        $this->app->singleton(ResetPasswordViewResponse::class, function () {
            return new class implements ResetPasswordViewResponse {
                public function toResponse($request)
                {
                    return view('auth.reset-password');
                }
            };
        });

        $this->app->singleton(TwoFactorChallengeViewResponse::class, function () {
            return new class implements TwoFactorChallengeViewResponse {
                public function toResponse($request)
                {
                    return view('auth.two-factor-challenge');
                }
            };
        });

        $this->app->singleton(ConfirmPasswordViewResponse::class, function () {
            return new class implements ConfirmPasswordViewResponse {
                public function toResponse($request)
                {
                    return view('auth.confirm-password');
                }
            };
        });
    }

    public function boot(): void
    {
        Fortify::createUsersUsing(CreateUserWithInvitation::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
    
        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;
            return Limit::perMinute(5)->by($email . $request->ip());
        });
    
        Fortify::loginView(function () {
            return view('auth.login');
        });
    
        Fortify::registerView(function (Request $request) {
            return view('auth.register', ['invitationCode' => $request->query('invitation_code')]);
        });
    
        Fortify::twoFactorChallengeView(function () {
            return view('auth.two-factor-challenge');
        });
    
        Fortify::resetPasswordView(function ($request) {
            return view('auth.reset-password', ['request' => $request]);
        });
    
        $this->app->bind(AttemptToAuthenticate::class, function ($app) {
            return new AttemptToAuthenticate(
                $app->make('auth')->guard(),
                $app->make(LoginRateLimiter::class)
            );
        });
    
        Fortify::authenticateUsing(function (Request $request) {
            $user = User::where('email', $request->email)->first();
    
            if ($user && Hash::check($request->password, $user->password)) {
                if ($user->login_notifications_enabled) {
                    $ipAddress = $request->ip();
                    $loginTime = now()->toDateTimeString();
                    $secureAccountUrl = route('password.request');
    
                    Mail::to($user->email)->send(new LoginNotificationMail($user, $ipAddress, $loginTime, $secureAccountUrl));
                }
                return $user;
            }
        });
    
        Fortify::authenticateThrough(function (Request $request) {
            $user = User::where('email', $request->email)->first();
            
            return array_filter([
                Features::enabled(Features::twoFactorAuthentication()) && $user && ($user->google2fa_secret || $user->two_factor_email_enabled) 
                    ? RedirectIfTwoFactorAuthenticatable::class 
                    : null,
                AttemptToAuthenticate::class,
            ]);
        });

        Fortify::authenticateUsing(function (Request $request) {
            $user = User::where('email', $request->email)->first();

            if ($user && Hash::check($request->password, $user->password)) {
                return $user;
            }

            return null;
        });

        // Register the email MFA routes with Fortify
        Fortify::twoFactorChallengeView(function () {
            $user = Auth::user();
            if ($user && $user->two_factor_email_enabled) {
                return redirect()->route('two-factor-email.challenge');
            }
            return view('auth.two-factor-challenge');
        });

        Fortify::authenticateThrough(function (Request $request) {
            return array_filter([
                Features::enabled(Features::twoFactorAuthentication()) ? RedirectIfTwoFactorAuthenticatable::class : null,
                AttemptToAuthenticate::class,
            ]);
        });
    }
}
