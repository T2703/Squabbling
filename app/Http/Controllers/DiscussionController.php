<?php

namespace App\Http\Controllers;

use App\Models\Discussion;
use App\Models\BoardModel;
use Illuminate\Http\Request;

class DiscussionController extends Controller
{
    /**
     * Display a listing of discussions for a specific board.
     */
    public function index(BoardModel $board)
    {
        $discussions = $board->discussions()->with('user')->latest()->get();
        return view('discussion.index', compact('board', 'discussions'));
    }

    /**
     * Show the form for creating a new discussion.
     */
    public function create(BoardModel $board)
    {
        return view('discussion.create', compact('board'));
    }

    /**
     * Store a newly created discussion.
     */
    public function store(Request $request, BoardModel $board)
    {
        $data = $request->validate([
            'content' => ['required', 'string']
        ]);

        $data['user_id'] = $request->user()->id;
        $data['board_model_id'] = $board->id;

        Discussion::create($data);

        return redirect()->route('board.show', $board)
            ->with('message', 'Discussion added successfully!');
    }

    /**
     * Display a single discussion.
     */
    public function show(BoardModel $board, Discussion $discussion)
    {
        $currentUser = auth()->user();
        
        if ($currentUser->isBlocking($discussion->user_id) || $discussion->user->isBlocking($currentUser->id)) {
            abort(403, 'You are not allowed to view this content.');
        }


        $discussion->load([
            'comments' => function ($query) {
                $query->whereNull('parent_id')->with('replies');
            }
        ]);

        return view('discussion.show', compact('board', 'discussion'));
    }

    /**
     * Show the form for editing the specified discussion.
     */
    public function edit(BoardModel $board, Discussion $discussion)
    {
        if ($discussion->user_id !== request()->user()->id) {
            abort(403); // Ensure only the owner can edit
        }

        return view('discussion.edit', compact('board', 'discussion'));
    }

    /**
     * Update the specified discussion.
     */
    public function update(Request $request, BoardModel $board, Discussion $discussion)
    {
        if ($discussion->user_id !== request()->user()->id) {
            abort(403);
        }

        $data = $request->validate([
            'content' => ['required', 'string'],
        ]);

        $discussion->update($data);

        return redirect()->route('board.show', $board)
            ->with('message', 'Discussion updated successfully!');
    }

    /**
     * Remove the specified discussion.
     */
    public function destroy(BoardModel $board, Discussion $discussion)
    {
        if ($discussion->user_id !== request()->user()->id && $board->user_id !== request()->user()->id) {
            abort(403);
        }

        $discussion->delete();

        return redirect()->route('board.show', $board)
            ->with('message', 'Discussion deleted successfully!');
    }
}
