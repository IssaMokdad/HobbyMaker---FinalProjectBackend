<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Going;
use App\Events;
use DB;
use App\Events\InvitationEvent;
use App\Http\Resources\Events as EventResource;
use App\User;
use App\Notifications\Invitation;

class GoingController extends Controller
{
    public function invite(Request $request){

        $validator = Validator::make($request->all(), [
            'user_id' => ['required', 'integer', 'min:1'],
            'friend_id' => ['required', 'integer', 'min:1'],
            'event_id' => ['required', 'integer', 'min:1'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 419);
        }
        $going = Going::create(['status'=>'pending','user_id'=>$request->input('friend_id'), 'event_id'=>$request->input('event_id')]);
        if ($going) {

            $userSendToInvitation = User::find($request->input('friend_id'));

            $userThatSentInvitation = User::find($request->input('user_id'));
            $targetEvent=Events::find($request->input('event_id'));
            $userSendToInvitation->notify(new Invitation($userThatSentInvitation, $targetEvent));

            event(new InvitationEvent($userThatSentInvitation, $targetEvent, $request->input('friend_id') ));

            return response()->json(['message' => 'success']);
        } else {
            return response()->json(['message' => 'error']);
        }
    }

    public function getPeopleNotInvitedToEvent(Request $request){
        $validator = Validator::make($request->all(), [
            'event_id' => ['required', 'integer', 'min:1'],
            'user_id'=>['required', 'integer', 'min:1'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 419);
        }

        $goingOrPendingIds = array_column(Events::find($request->input('event_id'))->goings->toArray(), 'user_id');
        
        $notInvitedFriends = DB::table('users')
        ->join('friends', 'users.id', '=', 'friends.friend_id')
        ->where('friends.status', 'accepted')
        ->where('friends.user_id', $request->input('user_id'))
        ->whereNotIn('friends.friend_id', $goingOrPendingIds)
        ->get();
        return response()->json(['data' => $notInvitedFriends]);
    }
    
    public function getGoingToEvents(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id'=>['required', 'integer', 'min:1'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 419);
        }     
        $eventIds = array_column(User::find($request->input('user_id'))->goings->where('status','going')->toArray(), 'event_id');
        return EventResource::collection(Events::orderBy('id', 'desc')->whereIn('id',$eventIds)->get());
        // $goingToEvents = DB::table('events')
        // ->join('goings', 'events.id', '=', 'goings.event_id')
        // ->where('goings.status', 'going')
        // ->where('goings.user_id', $request->input('user_id'))
        // ->get();
        // return response()->json(['data' => $goingToEvents]);
    }

    public function getInvitations(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id'=>['required', 'integer', 'min:1'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 419);
        }

        $eventIds = array_column(User::find($request->input('user_id'))->goings->where('status','pending')->toArray(), 'event_id');
        return EventResource::collection(Events::orderBy('id', 'desc')->whereIn('id',$eventIds)->get());
    }

    public function acceptInvitation(Request $request){

        $validator = Validator::make($request->all(), [
            'user_id' => ['required', 'integer', 'min:1'],
            'event_id' => ['required', 'integer', 'min:1'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 419);
        }
        $going = Going::where('event_id', $request->input('event_id'))->where('user_id', $request->input('user_id'))
        ->update(['status' => 'going']);
        if ($going) {

            return response()->json(['message' => 'success']);
        } else {
            return response()->json(['message' => 'error']);
        }
    }
    public function joinPublicEvent(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => ['required', 'integer', 'min:1'],
            'event_id' => ['required', 'integer', 'min:1'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 419);
        }

        $going = Going::create(['user_id'=>$request->input('user_id'), 'event_id'=>$request->input('event_id')]);
        if ($going) {

            return response()->json(['message' => 'success']);
        } else {
            return response()->json(['message' => 'error']);
        }
    }
    public function refuseInvitation(Request $request){

        $validator = Validator::make($request->all(), [
            'user_id' => ['required', 'integer', 'min:1'],
            'event_id' => ['required', 'integer', 'min:1'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 419);
        }
        $going = Going::where('event_id', $request->input('event_id'))->where('user_id', $request->input('user_id'))
        ->delete();
        if ($going) {

            return response()->json(['message' => 'success']);
        } else {
            return response()->json(['message' => 'error']);
        }
    }

}
