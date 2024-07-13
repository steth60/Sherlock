<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrustedDevice extends Model
{
    protected $fillable = ['user_id', 'device_name', 'device_token', 'expires_at'];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

