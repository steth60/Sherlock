<?php

// App\Events\ConsoleOutputUpdated.php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\PrivateChannel;

class ConsoleOutputUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $output;
    public $instanceId;

    public function __construct($instanceId, $output)
    {
        $this->instanceId = $instanceId;
        $this->output = $output;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('instance.' . $this->instanceId);
    }

    public function broadcastWith()
    {
        return [
            'output' => $this->output
        ];
    }
}

