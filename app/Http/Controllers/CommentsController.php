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

        return CommentResource::collection(Comments::where('post_id', $request->input('post-id'))->get());

    }

    public function remove(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer', 'min:1'],
            'post_id' => ['required', 'integer', 'min:1'],
            'user_id' => ['required', 'integer', 'min:1'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 419);
        }

        Comments::where('user_id', $request->input('user_id'))
            ->where('post_id', $request->input('post_id'))
            ->where('id', $request->input('id'))
            ->delete();
        return new PostResource(Post::find($request->input('post_id')));
    }

    public function create(Request $request)
    {

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



        return new PostResource(Post::find($request->input('post_id')));

    }

    public function edit(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer', 'min:1'],
            'comment' => ['required', 'string', 'max:255'],
            'post_id' => ['required', 'integer', 'min:1'],
            'user_id' => ['required', 'integer', 'min:1'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 419);
        }

        $comment = Comments::where('id', $request->input('id'))
            ->where('user_id', $request->input('user_id'))
            ->where('post_id', $request->input('post_id'))
            ->update(['comment' => $request->input('comment')]);

        if ($comment) {
            return new PostResource(Post::find($request->input('post_id')));
        } else {
            return response()->json(['message' => 'error']);
        }

        

    }
}
