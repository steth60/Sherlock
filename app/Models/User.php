<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\VerifyEmailNotification;

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
        'profile_photo',
        'login_notifications_enabled',
    ];  

    protected $attributes = [
        'login_notifications_enabled' => true,
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
        'two_factor_enabled' => 'boolean',
        'two_factor_email_enabled' => 'boolean',
        'email_mfa_code_expires_at' => 'datetime',
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


public function getProfilePhotoUrlAttribute()
{
    if ($this->profile_photo_type == 'upload' && $this->profile_photo) {
        return asset('storage/' . $this->profile_photo);
    }

    if ($this->profile_photo_type == 'icon' && $this->profile_photo) {
        return asset('storage/' . $this->profile_photo);
    }

    // For initials with color
    if ($this->profile_photo_type == 'initials' && $this->profile_photo) {
        return 'data:image/svg+xml;base64,' . base64_encode($this->generateInitialsSvg($this->profile_photo));
    }

    // Default behavior if no profile photo is set
    return 'data:image/svg+xml;base64,' . base64_encode($this->generateInitialsSvg('#cccccc'));
}

protected function generateInitialsSvg($bgColor)
{
    $initial = strtoupper(substr($this->name, 0, 1));
    return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100">
<rect width="100" height="100" fill="{$bgColor}" />
<text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-size="50" fill="#ffffff">{$initial}</text>
</svg>
SVG;
}

public function sendEmailVerificationNotification()
{
    $this->notify(new VerifyEmailNotification);
}

    // Define the relationship with groups
    public function groups()
    {
        return $this->belongsToMany(Group::class);
    }

    public function hasPermission($permission)
    {
        if (empty($permission)) {
            return true; // Assume items with no permission are visible to all
        }
        $result = false;
        foreach ($this->groups as $group) {
            if ($group->permissions->contains('name', $permission)) {
                $result = true;
                break;
            }
        }
        \Log::info("Checking permission: $permission for user {$this->id}. Result: " . ($result ? 'true' : 'false'));
        return $result;
    }
}
