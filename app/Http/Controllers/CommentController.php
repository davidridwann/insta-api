<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\User;
use Auth;

class CommentController extends Controller
{
    public function getComment($id) {
        $datas = [];
        $datas = Comment::where('post_id', $id)->where('comment_id', null)->get();
        $datas->map(function($data) {
            $user = User::where('id', $data->user_id)->first();
            $data->created_by = $user->name;

            $data->data = Comment::where('comment_id', $data->id)->get();

            $data->data->map(function($item) {
                $user = User::where('id', $item->user_id)->first();
                $item->created_by = $user->name;
            });
        });

        return response()->json([
            'success' => true,
            'data' => $datas,
            'message' => 'Success get comment'
        ], 200);
    }

    public function doComment(Request $request) {
        try {
            $input = $request->only('comment', 'post_id', 'comment_id');
            $input['user_id'] = Auth::user()->id;

            Comment::create($input);

            return response()->json([
                'success' => true,
                'message' => 'Success comment on post'
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
