<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laragear\WebAuthn\Models\WebauthnCredential as BaseWebauthnCredential;

class WebauthnCredential extends BaseWebauthnCredential
{
    protected $fillable = [
        'authenticatable_id',
        'authenticatable_type',
        'id',
        'user_handle',
        'public_key',
        'attestation_type',
        'transport',
        'aaguid',
        'credential_public_key',
        'last_login_at',
        'disabled_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'authenticatable_id')->where('authenticatable_type', User::class);
    }

    // You can keep any custom methods you need here
}