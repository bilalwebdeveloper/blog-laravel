<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Like;

class LikeController extends Controller
{
    public function store(Request $request, $article_id)
    {
        $like = Like::create([
            'article_id' => $article_id,
            'user_id' => $request->user()->id,
        ]);
        return response()->json($like, 201);
    }

    public function destroy(Request $request, $article_id)
    {
        Like::where('article_id', $article_id)
            ->where('user_id', $request->user()->id)
            ->delete();
        return response()->json(null, 204);
    }
}
