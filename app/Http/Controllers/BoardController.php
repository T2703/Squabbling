<?php

namespace App\Http\Controllers;

use App\Models\BoardModel;
use App\Models\Discussion;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;

class BoardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = request()->user();

        $ownBoards = BoardModel::query()
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate();

        $joinedBoards = $user->boards()
            ->where('board_models.user_id', '!=', $user->id) 
            ->orderBy('board_models.created_at', 'desc')
            ->get();
        
        $boards = $ownBoards->merge($joinedBoards);
        $users = User::all();

        return view('board.index', compact('boards', 'users'));
    }
    
    public function allBoards()
    {
        
        $boards = BoardModel::query()->orderBy('created_at', 'desc')->get();
        $users = User::all();
        return view('board.all', compact('boards', 'users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('board.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'tags' => ['nullable', 'string'] 
        ]);
    
        $data['user_id'] = $request->user()->id;
        $board = BoardModel::create($data);
    
        if (!empty($data['tags'])) {
            $tags = collect(json_decode($data['tags'], true))
                ->map(fn($tag) => trim($tag['value']))  
                ->filter()                    
                ->map(fn($tagName) => Tag::firstOrCreate(['name' => $tagName])->id);
    
            $board->tags()->sync($tags);
        }
    
        return to_route('board.show', $board)->with('message', 'Board was created successfully!');
    }    

    /**
     * Display the specified resource.
     */
    public function show(BoardModel $board)
    {
        $user = auth()->user(); 
        $discussions = $board->discussion()
            ->orderByDesc('created_at') 
            ->get()
            ->filter(function ($discussion) use ($user) {
                return !$user->isBlocking($discussion->user_id) &&
                    !$discussion->user->isBlocking($user->id);
            });
        return view('board.show', compact('board', 'user', 'discussions'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BoardModel $board)
    {
        if ($board->user_id !== request()->user()->id) {
            abort(403);
        }
        return view('board.edit', ['board' => $board]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BoardModel $board)
    {
        if ($board->user_id !== request()->user()->id) {
            abort(403);
        }

        $data = $request->validate([
            'title' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'tags' => ['nullable', 'string']
        ]);

       $board->update(($data));

       if (!empty($data['tags'])) {
            $tags = collect(json_decode($data['tags'], true))
                ->map(fn($tag) => trim($tag['value']))  
                ->filter()                    
                ->map(fn($tagName) => Tag::firstOrCreate(['name' => $tagName])->id);

            $board->tags()->sync($tags);
        }

       return to_route('board.show', $board)->with('message', 'Board was updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BoardModel $board)
    {
        if ($board->user_id !== request()->user()->id) {
            abort(403);
        }
        
        $board->delete();

        return to_route('board.index')->with('message', 'Note was deleted');
    }

    public function join(BoardModel $board)
    {
        $user = auth()->user();
    
        // Check if the user is already part of the board
        if ($board->users()->where('user_id', $user->id)->exists()) {
            return back()->with('message', 'You are already a member of this board.');
        }
    
        // Add the user to the board
        $board->users()->attach($user->id);
    
        return back()->with('message', 'You have successfully joined the board!');
    }

    public function leave(BoardModel $board)
    {
        $user = auth()->user();

        if ($board->users()->where('user_id', $user->id)->exists()) {
            // Detach user from the board
            $board->users()->detach($user->id);

            return back()->with('message', 'You have successfully left the board!');
        }

        return back()->with('error', 'You are not part of this board.');
    }

    public function search(Request $request)
    {
        $search = $request->input('search');
        $boards = BoardModel::where('title', 'like', "%$search%")->get();

        return view('board.index', compact('boards'));
    }

    public function searchOwn(Request $request)
    {
        $user = request()->user();
        $search = $request->input('search');

        $ownBoards = BoardModel::where('user_id', $user->id)
            ->where('title', 'like', "%$search%")
            ->orderBy('created_at', 'desc')
            ->get();

        $joinedBoards = $user->boards()
            ->where('board_models.user_id', '!=', $user->id) // Specify table for user_id
            ->where('board_models.title', 'like', "%$search%") // Apply search filter
            ->orderBy('board_models.created_at', 'desc')
            ->get();
        
        $boards = $ownBoards->merge($joinedBoards);

        return view('board.index', compact('boards'));
    }
    
    /**
     * Shows the most popular posts.
     */
    public function showPopular(BoardModel $board)
    {
        $user = auth()->user(); 
        $discussions = $board->discussion()
            ->withCount('likes')
            ->orderByDesc('likes_count') 
            ->get()
            ->filter(function ($discussion) use ($user) {
                return !$user->isBlocking($discussion->user_id) &&
                    !$discussion->user->isBlocking($user->id);
            });
        return view('board.showPopular', compact('board', 'user', 'discussions'));
    }
}
