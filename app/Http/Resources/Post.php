<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Post extends JsonResource
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
            'title' => $this->title,
            'image' => $this->image,
            'content' => $this->content,
            'created_at4' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user' => $this->user,
            'likes' => $this->likes,
            'comments' => $this->comments
        ];
    }
}
