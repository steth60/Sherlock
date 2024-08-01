<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebauthnCredential extends Model
{
    protected $fillable = [
        'user_id', 'credential_id', 'public_key', 'type', 'counter',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
