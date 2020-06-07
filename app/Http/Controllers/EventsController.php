<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Image;
use App\User;
use App\Going;
use App\Events;
use App\Http\Resources\Events as EventResource;
class EventsController extends Controller
{
    public function createEvent(Request $request){

        $validator = Validator::make($request->all(),
        [
            'user_id' => 'required|integer',
            'name'=>'required|string',
            'start_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'end_date' => 'required|date',
            'description' => 'required|string',
            'location' => 'required|string',
        ]
    );

    if ($validator->fails()) {
        return response()->json(['Validation errors' => $validator->errors()]);
    }

    $filename = date('Y-m-d-H-i-s') . 'userid=' . $request->input('user_id') . '.' . $request->file('image')->getClientOriginalExtension();
            Image::make($request->file('image')->getRealPath())->resize(468, 249)->save(public_path('images/' . $filename));

            $event = Events::create([
                'user_id' => $request->input('user_id'),
                'description' => $request->input('description'),
                'location' => $request->input('location'),
                'start_time' => $request->input('start_time'),
                'end_time' => $request->input('end_time'),
                'start_date' => $request->input('start_date'),
                'name'=>$request->input('name'),
                'end_date' => $request->input('end_date'),
                'image' => $filename,
            ]);
            // $event->fresh;
            $user = User::find($request->input('user_id'));
            $going = Going::create(['image'=>$user->image, 'user_id'=>$request->input('user_id'), 'event_id'=>$event->id]);
            if ($event && $going) {
                return response()->json(['message' => 'success']);
            } else {
                return response()->json(['message' => 'error']);
            }
    }

    public function getUserEvents(Request $request){
        $validate = new User;

        $error = $validate->validateUserRequest($request);
        if($error){
            return $error;
        }
        return EventResource::collection(Events::orderBy('id', 'desc')->where('user_id',$request->input('user_id') )->get());
        // $events = User::find($request->input('user_id'))->events;
        if ($events) {
            return response()->json(['events' => $events]);
        } else {
            return response()->json(['message' => 'error']);
        } 
    }

    public function deleteEvent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_id' => ['required', 'integer', 'min:1'],
            'user_id' => ['required', 'integer', 'min:1'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 419);
        }

        Events::where('user_id', $request->input('user_id'))
            ->where('id', $request->input('event_id'))
            ->delete();
            return response()->json(['message' => 'success']);
    }

    public function editEvent(Request $request){
        if ($request->hasFile('image')) {
        $validator = Validator::make($request->all(),
        [
            'event_id'=> 'required|integer',
            'user_id' => 'required|integer',
            'name'=>'required|string',
            'start_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'end_date' => 'required|date',
            'description' => 'required|string',
            'location' => 'required|string',
        ]
    );

    if ($validator->fails()) {
        return response()->json(['Validation errors' => $validator->errors()]);
    }

    $filename = date('Y-m-d-H-i-s') . 'userid=' . $request->input('user_id') . '.' . $request->file('image')->getClientOriginalExtension();
            Image::make($request->file('image')->getRealPath())->resize(468, 249)->save(public_path('images/' . $filename));

            $event = Events::where('id', $request->input('event_id'))
                ->update([
                'user_id' => $request->input('user_id'),
                'description' => $request->input('description'),
                'location' => $request->input('location'),
                'start_time' => $request->input('start_time'),
                'end_time' => $request->input('end_time'),
                'start_date' => $request->input('start_date'),
                'name'=>$request->input('name'),
                'end_date' => $request->input('end_date'),
                'image' => $filename,
            ]);
            // $event->fresh;
            if ($event) {
                return response()->json(['message' => 'success']);
            } else {
                return response()->json(['message' => 'error']);
            }
    }
    else{
        $validator = Validator::make($request->all(),
        [
            'event_id'=> 'required|integer',
            'user_id' => 'required|integer',
            'name'=>'required|string',
            'start_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'end_date' => 'required|date',
            'description' => 'required|string',
            'location' => 'required|string',
        ]
    );

    if ($validator->fails()) {
        return response()->json(['Validation errors' => $validator->errors()]);
    }

            $event = Events::where('id', $request->input('event_id'))
                ->update([
                'user_id' => $request->input('user_id'),
                'description' => $request->input('description'),
                'location' => $request->input('location'),
                'start_time' => $request->input('start_time'),
                'end_time' => $request->input('end_time'),
                'start_date' => $request->input('start_date'),
                'name'=>$request->input('name'),
                'end_date' => $request->input('end_date'),
            ]);
            // $event->fresh;
            if ($event) {
                return response()->json(['message' => 'success']);
            } else {
                return response()->json(['message' => 'error']);
            }
    }}

    public function getPublicEvents(Request $request){
        $validate = new User;

        $error = $validate->validateUserRequest($request);
        if($error){
            return $error;
        }
        return EventResource::collection(Events::orderBy('id', 'desc')->where('privacy','public')->get());
        // $events = User::find($request->input('user_id'))->events;
        // if ($events) {
        //     return response()->json(['events' => $events]);
        // } else {
        //     return response()->json(['message' => 'error']);
        // } 
    }
}
