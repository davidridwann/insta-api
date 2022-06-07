<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Like;
use Auth;

class LikeController extends Controller
{
    public function store($id) {
        try {
            $data = [];
            $data['user_id'] = Auth::user()->id;
            $data['pointer_id'] = $id;
            $data['type'] = 'post';

            Like::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Success like post'
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
