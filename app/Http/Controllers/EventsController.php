<?php

namespace App\Http\Controllers;

use App\Events;
use App\Going;
use Carbon;
use App\Http\Resources\Events as EventResource;
use App\User;
use Illuminate\Http\Request;
use Image;
use Validator;

class EventsController extends Controller
{
    public function createEvent(Request $request)
    {

        $validator = Validator::make($request->all(),
            [
                'user_id' => 'required|integer',
                'name' => 'required|string',
                'start_date' => 'required|date',
                'start_time' => 'required',
                'end_time' => 'required',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'end_date' => 'required|date',
                'description' => 'required|string',
                'location' => 'required|string',
                'privacy' => 'required|string',
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
            'name' => $request->input('name'),
            'end_date' => $request->input('end_date'),
            'privacy' => $request->input('privacy'),
            'image' => $filename,
        ]);
   
        $user = User::find($request->input('user_id'));
        $going = Going::create(['image' => $user->image, 'user_id' => $request->input('user_id'), 'event_id' => $event->id]);
        if ($event && $going) {
            return response()->json(['message' => 'success']);
        } else {
            return response()->json(['message' => 'error']);
        }
    }

    public function getUserEvents(Request $request)
    {
        $validate = new User;

        $error = $validate->validateUserRequest($request);
        if ($error) {
            return $error;
        }
        return EventResource::collection(Events::orderBy('id', 'desc')->where('user_id', $request->input('user_id'))->get());
       
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

    public function editEvent(Request $request)
    {
        if ($request->hasFile('image')) {
            $validator = Validator::make($request->all(),
                [
                    'event_id' => 'required|integer',
                    'user_id' => 'required|integer',
                    'name' => 'required|string',
                    'start_date' => 'required|date',
                    'start_time' => 'required',
                    'end_time' => 'required',
                    'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                    'end_date' => 'required|date',
                    'description' => 'required|string',
                    'privacy' => 'required|string',
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
                    'name' => $request->input('name'),
                    'end_date' => $request->input('end_date'),
                    'privacy' => $request->input('privacy'),
                    'image' => $filename,
                ]);
          
            if ($event) {
                return response()->json(['message' => 'success']);
            } else {
                return response()->json(['message' => 'error']);
            }
        } else {
            $validator = Validator::make($request->all(),
                [
                    'event_id' => 'required|integer',
                    'user_id' => 'required|integer',
                    'name' => 'required|string',
                    'start_date' => 'required|date',
                    'start_time' => 'required',
                    'end_time' => 'required',
                    'end_date' => 'required|date',
                    'description' => 'required|string',
                    'location' => 'required|string',
                    'privacy' => 'required|string',
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
                    'name' => $request->input('name'),
                    'end_date' => $request->input('end_date'),
                ]);
          
            if ($event) {
                return response()->json(['message' => 'success']);
            } else {
                return response()->json(['message' => 'error']);
            }
        }}

    public function getPublicEvents(Request $request)
    {
        $event = Events::where('privacy','public')
        ->where('user_id','!=',$request->originalDetectIntentRequest['payload']['user_id'])
        ->where('location', 'like', '%' . $request->queryResult['outputContexts'][0]['parameters']['location.original'][0] . '%')->first();

        
        if($event){
            $start_date=date_create($event->start_date);
            $end_date=date_create($event->end_date);

            return response()->json(["fulfillmentMessages" => array(
            array(
                "payload" => array(
                    'message' => date_format($start_date,"F d, Y ,") .$event->start_time. " - "   .date_format($end_date,"F d, Y ,"). $event->end_time , "platform" => "kommunicate", 'metadata' => array("contentType" => "300", "templateId" => "10", 'payload' => array(
                        array(
                            'title' => $event->name, 'subtitle' => $event->location, "titleExt" =>$event->goings->count() . " people going", "description" => $event->description, 'header' => array( "imgSrc" => "https://ded52e92cdd0.ngrok.io/images/".$event->image.""), 'buttons' => array(array('name' => 'Join the event', 'action' => array('type' => 'submit',
                            'payload' => array('formData'=>array('user_id'=>$request->originalDetectIntentRequest['payload']['user_id'], 'event_id'=>$event->id),"requestType"=> "json",'formAction' => "https://ded52e92cdd0.ngrok.io/api/join-event"))))))))))]);}
        else{
            return response()->json(["fulfillmentMessages" => array(array('payload'=>array("platform"=> "kommunicate",'message'=>'no events')))]);
        }

        $validate = new User;

        $error = $validate->validateUserRequest($request);
        if ($error) {
            return $error;
        }
        return EventResource::collection(Events::orderBy('id', 'desc')->where('user_id', '!=', $request->input('user_id'))->where('privacy', 'public')->get());

    }
}
