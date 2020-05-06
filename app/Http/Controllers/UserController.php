<?php

namespace App\Http\Controllers;
use App\User;
use Illuminate\Http\Request;
use DB;
use App\Http\Resources\User as UserResource;

class UserController extends Controller
{
    public function getUsersWithSameHobbyAndAddress(Request $request){
        //get the user
        //get the friends ids and add the authenticated user id to the array
        //get the users having common hobby who are at the same country and city
        //exlude the friends ids and the authenticated user id from the result
        $user = User::find($request->input('user_id'));
        
        $friendsIds = array_column($user->friend->toArray(), 'friend_id');

        $userHobbies = array_column($user->hobby->toArray(), 'hobby');

        $friendsIds[] = $request->input('user_id');

        $users = DB::table('users')
        ->join('hobbies', 'users.id', '=', 'hobbies.user_id')
        ->where('users.country', $user->country)
        ->where('users.city',$user->city)
        ->whereIn('hobbies.hobby', $userHobbies)
        ->whereNotIn('users.id', $friendsIds )
        ->select('users.id','hobbies.hobby','users.first_name','users.last_name')
        ->get();

        return response()->json(['data'=>$users]);
    }

    public function getUserInfo(Request $request){
        return new UserResource(User::find($request->input('user-id')));
    }
}