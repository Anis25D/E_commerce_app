<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewSeller implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $sellerId;
    // public $notification;
    public $photo;
    public $alertId;
    public $time;
    public $status;
    public $content;
    public $type;

    public function __construct($seller, $notification)
    {
        // Assign seller data
        $this->sellerId = $seller->id;
        $this->alertId = $notification->id; // Assuming notification has an 'id' attribute
        $this->time = $notification->created_at; // Assuming you want to broadcast notification creation time
        $this->status = $notification->status;
        $this->content = $notification->content;
        $this->type = $notification->type;
        $this->photo = $notification->photo;
        
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('adminAlert');
    }

}
