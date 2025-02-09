<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InvitationEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $event;
    public $userSendToId;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user, $event, $userSendToId)
    {
        $this->user = $user;
        $this->event = $event;
        $this->userSendToId = $userSendToId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('my-channel.' . $this->userSendToId);
        // return ['my-channel'];
    }

    public function broadcastWith()
    {
        // This must always be an array. Since it will be parsed with json_encode()
        return [
            'data' => ['message' => $this->user->first_name . ' ' . $this->user->last_name . ' ' . 'invited you to a ' . $this->event->name . " event.",
            'user' => $this->user],
            'type' => 'App\Notifications\Invitation',
            'notifiable_id' => 'i',

        ];
    }

    public function broadcastAs()
    {
        return 'invitation-request';
    }
}
