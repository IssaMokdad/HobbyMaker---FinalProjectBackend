<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \Pusher\Pusher;
use App\User;
class TestController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){}


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */


    public function authenticate(Request $request){
        $socketId = $request->socket_id;
        $channelName = $request->channel_name;

        $pusher = new Pusher('bb8b5c6a21ad2865dda7', 'bdaa94858753ff96b965', '1002211', [
            'cluster' => 'ap2',
            'encrypted' => true,
        ]);

        $presence_data = ['first_name' => User::find($request->input('user_id'))->first_name];
        $key = $pusher->presence_auth($channelName, $socketId, $request->input('user_id'), $presence_data);
        return response($key);
    }
}
