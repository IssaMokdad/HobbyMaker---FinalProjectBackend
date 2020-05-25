<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Validator;

class YoutubeVideos extends Model
{
    protected $fillable = ['video_id', 'user_id'];

public function user()
{
    return $this->belongsTo(User::class);
}

public function validateVideoRequest($request){
    $validator = Validator::make($request->all(), [
        'user_id' => ['required', 'integer', 'min:1'],
        'video_id' => ['required', 'string'],
    ]);
    if ($validator->fails()) {
        return response()->json($validator->messages(), 419);
    }
}
}