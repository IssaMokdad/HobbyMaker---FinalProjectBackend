<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'country' => $this->country,
            'city'   => $this->city,
            'birthday' => $this->birthday,
            'posts'=>$this->post,
            'image'=>$this->image,
            'cover_photo'=>$this->cover_photo,
            'avatar'=>$this->avatar,
            'hobbies'=>$this->hobby,
            'friends'=>$this->friend,
            'videos'=>$this->video,
            'saved_posts'=>$this->savedPost
        ];
    }
}
