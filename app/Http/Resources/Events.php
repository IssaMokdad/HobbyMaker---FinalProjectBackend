<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Going;
class Events extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
        
        'id' => $this->id,
        'user_id' => $this->user_id,
        'description' => $this->description,
        'start_date' => $this->start_date,
        'name'   => $this->name,
        'end_date' => $this->end_date,
        'start_time'=>$this->start_time,
        'image'=>$this->image,
        'end_time'=>$this->end_time,
        'location'=>$this->location,
        'privacy'=>$this->privacy,
        'going'=>Going::where('event_id', $this->id)->where('status','going')->join('users', 'users.id', '=', 'goings.user_id')->get(),
        // 'going'=>$this->goings->where('status', 'going'),

        ];
    }
}
