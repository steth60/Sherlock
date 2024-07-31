<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'subject',
        'contact',
        'state',
        'status',
        'priority',
        'created_date',
        'assigned_to',
        'company',
        'urgency',
        'category',
        'sub_category',
    ];

    // Optionally, you can specify the table if it's different from the default
    protected $table = 'tickets';

    // If the created_at and updated_at columns are not used, you can disable the timestamps
    public $timestamps = true;

    // Optionally, if created_date is stored as a string, you can cast it to a datetime
    protected $casts = [
        'created_date' => 'datetime',
    ];
}
