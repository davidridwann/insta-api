<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use Auth;

class CommentController extends Controller
{
    public function doComment(Request $request) {
        try {
            $input = $request->only('comment', 'post_id', 'comment_id');
            $input['user_id'] = Auth::user()->id;

            Comment::create($input);

            if ($input['comment_id']) {
                pushActivity(Auth::user()->id, $input['comment_id'], 'on_comment');
            } else {
                pushActivity(Auth::user()->id, $input['post_id'], 'on_post');
            }

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
