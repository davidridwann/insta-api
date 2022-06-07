<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Post;
use Auth;

class PostController extends Controller
{
    public function get() {
        $posts = [];
        $posts = Post::where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->take(5)->get();
        $followingPost = Follow::where('user_id', Auth::user()->id)->get();

        $followPost->map(function($data) use ($posts) {
            $currentPost = Post::where('user_id', $data->user_id)->orderBy('created_at', 'desc')->get();

            $posts->push($currentPost);
        });

        return response()->json([
            'success' => true,
            'data' => $posts,
            'message' => 'success get posts'
        ], 200);
    }

    public function show($id) {
        $post = [];
        $post = Post::where('id', $id)->first();

        return response()->json([
            'success' => true,
            'data' => $post,
            'message' => 'success get post'
        ], 200);
    }

    public function store(Request $request) {
        try {
            $input = $request->only('post', 'caption', 'hide_comment', 'hide_like', 'is_archive');
            $input['user_id'] = Auth::user()->id;

            $validator = Validator::make($input, [
                'post' => 'required',
            ]);

            if($validator->fails()){
                return sendError('Something went wrong!', $validator->errors());

                return response()->json([
                    'success' => false,
                    'message' => 'Something went wrong!',
                    'data' => $validator->errors()
                ], 404);
            }

            Post::create($input);

            return response()->json([
                'success' => true,
                'message' => 'Success create a new post'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!',
                'data' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id) {
        try {
            $input = $request->only('post', 'caption', 'hide_comment', 'hide_like', 'is_archive');
            $input['is_edit'] = true;

            $validator = Validator::make($input, [
                'post' => 'required',
            ]);

            if($validator->fails()){
                return sendError('Something went wrong!', $validator->errors());

                return response()->json([
                    'success' => false,
                    'message' => 'Something went wrong!',
                    'data' => $validator->errors()
                ], 404);
            }

            Post::where('id', $id)->update($input);

            return response()->json([
                'success' => true,
                'message' => 'Success update a post'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!',
                'data' => $e->getMessage()
            ], 404);
        }
    }

    public function destroy($id) {
        try {
            Post::where('id', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Success delete a post'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!',
                'data' => $e->getMessage()
            ], 404);
        }
    }
}
