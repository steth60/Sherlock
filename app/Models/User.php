<?php

// App\Models\User.php

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

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function departments()
    {
        return $this->belongsToMany(Department::class);
    }

    public function hasRole($role)
    {
        return $this->roles()->where('name', $role)->exists();
    }

    public function hasDepartment($department)
    {
        return $this->departments()->where('name', $department)->exists();
    }
    
}

