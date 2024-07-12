<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instance extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'github_url',
        'start_command',
        'description',
        'status',
        'pid'
    ];

    public function consoleOutputs()
    {
        return $this->hasMany(ConsoleOutput::class);
    }
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
    public function notes()
    {
        return $this->hasMany(Note::class);
    }
}