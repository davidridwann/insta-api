<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;
use App\Models\Post;
use App\Models\User;
use App\Models\Like;
use App\Models\Follow;
use Auth;

class PostController extends Controller
{
    public function get() {
        $posts = [];
        $posts = Post::where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->take(5)->get();
        $posts->map(function($post) {
            $totalLike = Like::where('pointer_id', $post->id)->count();

            $checkLike = Like::where('pointer_id', $post->id)->where('user_id', Auth::user()->id)->first();

            if ($checkLike) {
                $post->is_like = true;
            } else {
                $post->is_like = false;
            }

            $post->created_by = Auth::user()->username;
            $post->total_like = $totalLike;

        });

        $followingPost = Follow::where('user_id', Auth::user()->id)->get();

        $followingPost->map(function($data) use ($posts) {
            $currentPost = Post::where('user_id', $data->user_id)->orderBy('created_at', 'desc')->get();
            $currentPost->map(function($item) {
                $userData = User::where('id', $item->user_id)->first();
                $totalLike = Like::where('pointer_id', $item->id)->count();

                $checkLike = Like::where('pointer_id', $item->id)->where('user_id', Auth::user()->id)->first();

                if ($checkLike) {
                    $post->is_like = true;
                } else {
                    $post->is_like = false;
                }

                $item->created_by = $userData->username;
                $item->total_like = $totalLike;
            });

            $posts->push($currentPost);
        });

        $posts = $this->paginate($posts);

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
            $input = $request->only('caption', 'hide_comment', 'hide_like', 'is_archive', 'post');
            $image = $request->file('post');

            $validator = Validator::make($input, [
                'post' => 'required|mimes:jpeg,jpg,png',
            ]);

            $file_name = strtotime(Carbon::now()) . '.' . $image->getClientOriginalExtension();
            $image->move('post', $file_name);

            $input['user_id'] = Auth::user()->id;
            $input['post'] = 'post/'. $file_name;

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
            $input = $request->only('caption', 'hide_comment', 'hide_like', 'is_archive', 'post');
            $image = $request->file('post');

            $validator = Validator::make($input, [
                'post' => 'mimes:jpeg,jpg,png',
            ]);

            if($validator->fails()){
                return sendError('Something went wrong!', $validator->errors());

                return response()->json([
                    'success' => false,
                    'message' => 'Something went wrong!',
                    'data' => $validator->errors()
                ], 404);
            }

            if (empty($image)) {
                $file_name = strtotime(Carbon::now()) . '.' . $file->getClientOriginalExtension();
                $file->move('post', $file_name);
                $input['post'] = 'post/'. $file_name;
            } else {
                $currentData = Post::where('id', $id)->first();
                $input['post'] = $currentData->post;
            }

            $input['is_edit'] = true;

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
            Like::where('pointer_id', $id)->delete();
            Comment::where('post_id', $id)->delete();
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

    protected function paginate($items, $perPage = 10, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
}
