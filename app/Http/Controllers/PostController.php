<?php

namespace App\Http\Controllers;

use App\Http\Resources\Post as PostResource;
use App\Post;
use App\User;
use Validator;
use Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class PostController extends Controller
{

    public function get(Request $request)
    {
        //Get the latest posts of the authenticated user and his/her friends. Also, onEachBottomScroll, we send 5 more posts 
        $friendsIds = array_column(User::find($request->input('user_id'))->friend->toArray(), 'friend_id');
        $friendsIds[] = $request->input('user_id');
        return PostResource::collection(Post::orderBy('id', 'desc')->whereIn('user_id', $friendsIds)->take($request->input('page')*5)->get());

    }
    public function add(Request $request)
    {

        if ($request->hasFile('image')) {



            $validator = Validator::make($request->all(), [
                'content' => 'required',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'user_id' => ['required', 'integer', 'min:1'],
            ]);
    
            if ($validator->fails()) {
                return response()->json($validator->messages(), 419);
            }




            // $file = $request->file('image');
            // $extension = $file->getClientOriginalExtension();
            // $filename = time() . "." . $extension;
            // $file->move('images/', $filename);

            $filename = date('Y-m-d-H-i-s').'userid='.$request->input('user_id').'.'.$request->file('image')->getClientOriginalExtension();
            Image::make($request->file('image')->getRealPath())->resize(468, 249)->save(public_path('images/'.$filename));
            
            // $thumbnailpath = public_path('storage/profile_images/thumbnail/'.$filenametostore);
            // $img = Image::make($thumbnailpath)->resize(400, 150, function($constraint) {
            //     $constraint->aspectRatio();
            // });


            $post = Post::create([
                'user_id' => $request->input('user_id'),
                'content' => $request->input('content'),
                'image' => $filename,
            ]);
            return response()->json(['post' => $post]);} 
            else {

            $validator = Validator::make($request->all(), [
                'content' => 'required',
                'user_id' => ['required', 'integer', 'min:1'],
            ]);
    
            if ($validator->fails()) {
                return response()->json($validator->messages(), 419);
            }

            $post = Post::create([
                'user_id' => $request->input('user_id'),
                'content' => $request->input('content'),
            ]);
            return response()->json(['post' => $post]);
        }
    }

    public function edit(Request $request)
    {
        if ($request->hasFile('image')) {


            $validator = Validator::make($request->all(), [
                'content' => 'required',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'user_id' => ['required', 'integer', 'min:1'],
                'post_id' => ['required', 'integer', 'min:1'],
            ]);

            if ($validator->fails()) {
                return response()->json($validator->messages(), 419);
            }

            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension();
            $filename = time() . "." . $extension;
            $file->move('images/', $filename);
            $post = Post::where('id', $request->input('post_id'))->where('user_id', $request->input('user_id'))
                ->update(['content' => $request->input('content'), 'image' => $filename]);
            if ($post) {
                return response()->json(['message' => 'success']);
            } else {
                return response()->json(['message' => 'error']);
            }
        } else {

            $validator = Validator::make($request->all(), [
                'content' => 'required',
                'user_id' => ['required', 'integer', 'min:1'],
                'post_id' => ['required', 'integer', 'min:1'],
            ]);

            if ($validator->fails()) {
                return response()->json($validator->messages(), 419);
            }

            $post = Post::where('id', $request->input('post_id'))->where('user_id', $request->input('user_id'))
                ->update(['content' => $request->input('content')]);
            if ($post) {
                return response()->json(['message' => 'success']);
            } else {
                return response()->json(['message' => 'error']);
            }
        }

    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'post_id' => ['required', 'integer', 'min:1'],
            'user_id' => ['required', 'integer', 'min:1'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 419);
        }
        $post = Post::where('id', $request->input('post_id'))->where('user_id', $request->input('user_id'))
            ->delete();
        if($post){
            return response()->json(['message' => 'success']);
        }
        else{
            return response()->json(['message' => 'error']);
        }
    }
}
