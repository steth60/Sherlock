<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $fillable = ['date', 'message'];

    // If using timestamps, Laravel automatically manages created_at and updated_at
    public $timestamps = true;

    // If you need custom date formats
    protected $dates = ['created_at', 'updated_at'];
}