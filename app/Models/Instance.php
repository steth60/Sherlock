<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instance extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'github_url', 'start_command', 'status', 'pid'];

    public function consoleOutputs()
    {
        return $this->hasMany(ConsoleOutput::class);
    }
}