<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Likes;
use App\Post;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Post as PostResource;
class LikesController extends Controller
{
    public function add(Request $request){

        $validator = Validator::make($request->all(), [
            'post_id' => ['required', 'integer', 'min:1'],
            'user_id' => ['required', 'integer', 'min:1'],
        ]);

        if ($validator->fails()) {    
            return response()->json($validator->messages(), 419);
        }
        $like = Likes::create([
            'post_id' => $request->input('post_id'),
            'user_id' => $request->input('user_id'),
        ]);

        return new PostResource(Post::find($request->input('post_id')));
    }

    public function remove(Request $request){

        $validator = Validator::make($request->all(), [
            'post_id' => ['required', 'integer', 'min:1'],
            'user_id' => ['required', 'integer', 'min:1'],
        ]);
        if ($validator->fails()) {    
            return response()->json($validator->messages(), 419);
        }
        Likes::where('user_id',$request->input('user_id'))
               ->where('post_id', $request->input('post_id'))
               ->delete();
        return new PostResource(Post::find($request->input('post_id')));

    }
}
