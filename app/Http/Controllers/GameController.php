<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;

class GameController extends Controller
{
    // Show homepage with all games
public function index(Request $request)
{
    $search = $request->input('search');
    
    $games = Game::query()
        ->when($search, function ($query, $search) {
            return $query->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('description', 'LIKE', "%{$search}%");
        })
        // Remove this line: ->where('status', 'active')
        ->get();
    
    return view('welcome', compact('games', 'search'));
}

    // Show topup page for specific game
    public function topup($id)
    {
        $game = Game::with('topupOptions')->findOrFail($id);
        return view('games.topup', compact('game'));
    }

    // Add this new method
    public function searchGames(Request $request)
    {
        $query = $request->input('q');
        
        $games = Game::query()
            ->where('name', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->limit(5)
            ->get(['id', 'name', 'logo']);
        
        return response()->json($games);
    }
}

