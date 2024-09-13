<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;

class CommentController extends Controller
{
    public function store(Request $request, $article_id)
    {
        $comment = Comment::create([
            'article_id' => $article_id,
            'user_id' => $request->user()->id,
            'comment' => $request->comment,
        ]);
        return response()->json($comment, 201);
    }

    public function destroy($id)
    {
        Comment::destroy($id);
        return response()->json(null, 204);
    }
}

