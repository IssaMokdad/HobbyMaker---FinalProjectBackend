<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AcceptFriendRequestEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userSendToRequest;
    public $userThatSentRequest;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($userThatSentRequest, $userSendToRequest)
    {
        $this->userSendToRequest = $userSendToRequest;
        $this->userThatSentRequest = $userThatSentRequest;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('my-channel.' . $this->userThatSentRequest->id);
        // return ['my-channel'];
    }

    public function broadcastWith()
    {
        // This must always be an array. Since it will be parsed with json_encode()
        return [
            'data' => ['message' => $this->userSendToRequest->first_name . ' ' . $this->userSendToRequest->last_name . ' ' . 'accepted your friend request.',
            'user' => $this->userSendToRequest],
            'type' => 'App\Notifications\AcceptFriendRequest',
            'notifiable_id' => 'i',

        ];
    }

    public function broadcastAs()
    {
        return 'add-request';
    }
}
