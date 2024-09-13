<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SearchHistory;

class SearchHistoryController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(SearchHistory::where('user_id', $request->user()->id)->get(), 200);
    }

    public function store(Request $request)
    {
        $search = SearchHistory::create([
            'user_id' => $request->user()->id,
            'search_term' => $request->search_term,
        ]);
        return response()->json($search, 201);
    }
}
