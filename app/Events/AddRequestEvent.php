<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AddRequestEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userSendToRequest;
    public $userThatSentRequest;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($userSendToRequest, $userThatSentRequest)
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
        return new PrivateChannel('my-channel.' . $this->userSendToRequest->id);
        // return ['my-channel'];
    }

    public function broadcastWith()
    {
        // This must always be an array. Since it will be parsed with json_encode()
        return [
          'data' => ['message'=>$this->userThatSentRequest->first_name . ' ' . $this->userThatSentRequest->last_name . ' ' . 'added you.',
          'user'=>$this->userThatSentRequest],
          'type' => 'App\Notifications\AddRequest',
          'notifiable_id'=>'i'
  
      ];
    }

    public function broadcastAs()
    {
        return 'add-request';
    }
}