<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'instance_id', 'action', 'months', 'days', 'hours', 'minutes', 'description', 'enabled',
    ];

    protected $casts = [
        'months' => 'array',
        'days' => 'array',
        'hours' => 'array',
        'minutes' => 'array',
    ];

    public function instance()
    {
        return $this->belongsTo(Instance::class);
    }

    public function getNextRunTime()
    {
        $cronExpression = sprintf(
            '%s %s * * %s',
            implode(',', $this->minutes),
            implode(',', $this->hours),
            implode(',', $this->days)
        );

        $cron = \Cron\CronExpression::factory($cronExpression);
        return $cron->getNextRunDate()->format('Y-m-d H:i:s');
    }
}
