<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Friend;
use DB;
use Validator;
class FriendController extends Controller
{

    public function add(Request $request){
        $validator = Validator::make($request->all(), [
            'friend_id' => ['required', 'integer', 'min:1'],
            'user_id' => ['required', 'integer', 'min:1'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 419);
        }
        $friend = Friend::create([
            'friend_id' => $request->input('friend_id'),
            'user_id' => $request->input('user_id'),
            'status'  => 'pending'
        ]);
        if($friend){
            return response()->json(['message'=>'success']);}
            else{
                return response()->json(['data'=>'error']);
            }
    }
}
