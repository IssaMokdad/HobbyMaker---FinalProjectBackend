<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SavedPost;
use Validator;
use App\User;
use App\Post;
use App\Http\Resources\Post as PostResource;
class SavedPostController extends Controller
{
    public function savePost(Request $request)
    {


        $validate = new SavedPost;

        $error = $validate->validateSavedPostRequest($request);
        if($error){
            return $error;
        }


        $post = SavedPost::create([
            'post_id' => $request->input('post_id'),
            'user_id' => $request->input('user_id'),
        ]);
        if($post){
            return response()->json(['message' => 'success']);
        }
        else{
            return response()->json(['message' => 'error']);
        }
        
    }
    public function unsavePost(Request $request){

        $validate = new SavedPost;

        $error = $validate->validateSavedPostRequest($request);
        if($error){
            return $error;
        }

        $post = SavedPost::where('user_id', $request->input('user_id'))
            ->where('post_id', $request->input('post_id'))
            ->delete();
        if($post){
            return response()->json(['message' => 'success']);
        }
        else{
            return response()->json(['message' => 'error']);
        }
    }

    public function getSavedPost(Request $request)
    {
        $validate = new User;

        $error = $validate->validateUserRequest($request);
        if($error){
            return $error;
        }
        //Get the saved posts details
        $savedPostsIds = array_column(User::find($request->input('user_id'))->savedPost->toArray(), 'post_id');

        return PostResource::collection(Post::orderBy('id', 'desc')->whereIn('id', $savedPostsIds)->get());

    }
}
