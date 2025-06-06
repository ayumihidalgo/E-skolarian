<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NewAnnouncement
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $announcement;
    public $audience;
    public $audience_students;

    /**
     * Create a new event instance.
     */
    public function __construct($announcement, $audience, $audience_students = null)
    {
        $this->announcement = $announcement;
        Log::info('NewAnnouncement event is being sent to listener with announcement ID: ' . $announcement->id);
        $this->audience = $audience;
        $this->audience_students = $audience_students;
        Log::info('NewAnnouncement event constructed with announcement ID: ' . $announcement->id);
    }

}
