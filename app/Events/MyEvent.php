<?php
namespace App\Events;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;


class MyEvent implements ShouldBroadcast
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public $user;

  public function __construct($user)
  {
      $this->user = $user;
  }

  public function broadcastOn()
  {
      return ['my-channel'];
  }
  public function broadcastWith()
  {
      // This must always be an array. Since it will be parsed with json_encode()
      return [
        'data' => ['message'=>$this->user->first_name . ' ' . $this->user->last_name . ' ' . 'added you.',
        'user'=>$this->user],
        'type' => 'App\Notifications\AddRequest',
        'notifiable_id'=>'i'

    ];
  }
  
  public function broadcastAs()
  {
      return 'my-event';
  }
}