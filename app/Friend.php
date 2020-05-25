<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Validator;

class Friend extends Model
{
    protected $fillable = ['user_id', 'friend_id', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function validateFriendRequest($request)
    {
        $validator = Validator::make($request->all(), [
            'friend_id' => ['required', 'integer', 'min:1'],
            'user_id' => ['required', 'integer', 'min:1'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 419);
        }
    }
}
