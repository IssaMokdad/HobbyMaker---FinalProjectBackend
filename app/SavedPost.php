<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Validator;
class SavedPost extends Model
{
    protected $fillable = ['post_id', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function validateSavedPostRequest($request){
        $validator = Validator::make($request->all(), [
            'user_id' => ['required', 'integer', 'min:1'],
            'post_id' => ['required', 'integer'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 419);
        }
    }
}
