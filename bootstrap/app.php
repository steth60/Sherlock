<?php
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\{
    Authenticate,
    EncryptCookies,
    PreventRequestsDuringMaintenance,
    RedirectIfAuthenticated,
    TrimStrings,
    TrustHosts,
    TrustProxies,
    VerifyCsrfToken,
    EnsureMfaEnabled,
    RedirectToMfaSetup,
    LoadMenuItems,
    ForcePasswordChange,
    CheckPasswordChange,
    CheckPermission
};
use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;
use Illuminate\Auth\Middleware\RequirePassword;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Http\Middleware\SetCacheHeaders;
use Illuminate\Foundation\Http\Middleware\ValidatePostSize;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Routing\Middleware\ValidateSignature;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Spark\Http\Middleware\VerifyBillableIsSubscribed;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            SubstituteBindings::class,
            LoadMenuItems::class,
            CheckPasswordChange::class, 
            ThrottleRequests::class,
            // Add this line to ensure it runs on web routes
        ]);
        $middleware->alias([
            'auth' => Authenticate::class,
            'auth.basic' => AuthenticateWithBasicAuth::class,
            'auth.session' => AuthenticateSession::class,
            'cache.headers' => SetCacheHeaders::class,
            'can' => Authorize::class,
            'guest' => RedirectIfAuthenticated::class,
            'password.confirm' => RequirePassword::class,
            'precognitive' => HandlePrecognitiveRequests::class,
            'signed' => ValidateSignature::class,
            'subscribed' => VerifyBillableIsSubscribed::class,
            'verified' => EnsureEmailIsVerified::class,
            'mfa.setup' => RedirectToMfaSetup::class,
            'mfa' => EnsureMfaEnabled::class,
            'permission' => CheckPermission::class,
            'checkPasswordChange' => CheckPasswordChange::class,
            'throttle' => ThrottleRequests::class,
        ]);


        $middleware->priority([
            HandlePrecognitiveRequests::class,
            TrustHosts::class,
            TrustProxies::class,
            PreventRequestsDuringMaintenance::class,
            ValidatePostSize::class,
            TrimStrings::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            EnsureFrontendRequestsAreStateful::class,
            SubstituteBindings::class,
            Authenticate::class,
            Authorize::class,
            CheckPasswordChange::class,
            CheckPermission::class,
            ThrottleRequests::class,
        ]);
    })
    ->withExceptions(function ($exceptions) {
        // Exception handling configuration
    })
    ->create();