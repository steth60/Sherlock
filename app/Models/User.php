<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use App\Notifications\ResetPasswordNotification;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'google2fa_secret', // For Google 2FA
        'two_factor_confirmed_at',
        'department', // Added department field
        'active', // Added active field
        'force_password_change',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
        'google2fa_secret',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'two_factor_confirmed_at' => 'datetime',
        'active' => 'boolean',
        'force_password_change' => 'boolean',
    ];

    public function hasVerifiedMfa()
    {
        return !is_null($this->two_factor_confirmed_at);
    }

    public function passwordHistories()
    {
        return $this->hasMany(PasswordHistory::class);
    }

    public function trustedDevices()
    {
        return $this->hasMany(TrustedDevice::class);
    }

    public function sendPasswordResetNotification($token)
{
    $url = url(route('password.reset', [
        'token' => $token,
        'email' => $this->getEmailForPasswordReset(),
    ], false));

    $this->notify(new ResetPasswordNotification($url));
}

use App\Notifications\VerifyEmailNotification;

public function sendEmailVerificationNotification()
{
    $this->notify(new VerifyEmailNotification);
}

    // Define the relationship with groups
    public function groups()
    {
        return $this->belongsToMany(Group::class);
    }

    // Method to check if a user has a specific permission
    public function hasPermission($permission)
    {
        foreach ($this->groups as $group) {
            if ($group->permissions->contains('name', $permission)) {
                return true;
            }
        }
        return false;
    }
}
