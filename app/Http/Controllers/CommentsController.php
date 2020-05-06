<?php

namespace App\Http\Controllers;

use App\Comments;
use App\Http\Resources\Comment as CommentResource;
use App\Http\Resources\Post as PostResource;
use App\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentsController extends Controller
{

    public function show(Request $request)
    {
        
        return CommentResource::collection(Comments::where('post_id',$request->input('post-id'))->get());

    }
    public function create(Request $request)
    {
        // var_dump(json_decode($request->json()->all()));

        // $data = $request->json()->all();
        $validator = Validator::make($request->all(), [
            'comment' => ['required', 'string', 'max:255'],
            'post_id' => ['required', 'integer', 'min:1'],
            'user_id' => ['required', 'integer', 'min:1'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 419);
        }
        $comment = Comments::create([
            'comment' => $request->input('comment'),
            'post_id' => $request->input('post_id'),
            'user_id' => $request->input('user_id'),
        ]);

        // $datas=[];
        // array_push($datas, $data, ['id'=>Auth::id()]);

        return new PostResource(Post::find($request->input('post_id')));
        // return response()->json(['foo'=>'bar']);
    }
}
