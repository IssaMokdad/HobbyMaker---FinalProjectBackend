<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Validator;

class Likes extends Model
{
    protected $fillable = ['user_id', 'post_id'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function validateLikeRequest($request)
    {
        $validator = Validator::make($request->all(), [
            'post_id' => ['required', 'integer', 'min:1'],
            'user_id' => ['required', 'integer', 'min:1'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 419);
        }
    }
}
