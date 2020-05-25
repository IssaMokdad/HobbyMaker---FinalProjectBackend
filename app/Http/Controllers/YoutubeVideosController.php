<?php

namespace App\Http\Controllers;

use App\YoutubeVideos;
use Illuminate\Http\Request;
use Validator;

class YoutubeVideosController extends Controller
{
    public function saveVideo(Request $request)
    {

        $validate = new YoutubeVideos;

        $validate->validateVideoRequest($request);

        $video = YoutubeVideos::create([
            'video_id' => $request->input('video_id'),
            'user_id' => $request->input('user_id'),
        ]);
        if($video){
            return response()->json(['message' => 'success']);
        }
        else{
            return response()->json(['message' => 'error']);
        }
        
    }
    public function unsaveVideo(Request $request){

        $validate = new YoutubeVideos;

        $validate->validateVideoRequest($request);

        $video = YoutubeVideos::where('user_id', $request->input('user_id'))
            ->where('video_id', $request->input('video_id'))
            ->delete();
            
        if($video){
            return response()->json(['message' => 'success']);
        }
        else{
            return response()->json(['message' => 'error']);
        }
    }

}
