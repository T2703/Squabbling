<?php

namespace App\Http\Controllers;

use App\Models\CommentModel;
use App\Models\Discussion;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Discussion $discussion)
    {
        $request->validate([
            'content' => 'required|string|max:500',
        ]);

        $discussion->comments()->create([
            'content' => $request->content,
            'user_id' => auth()->id(),
        ]);

        return back()->with('message', 'Comment added successfully!');
    }

    public function destroy(CommentModel $comment)
    {
        if ($comment->user_id === auth()->id()) {
            $comment->delete();
            return back()->with('message', 'Comment deleted successfully!');
        }

        return back()->with('error', 'You can only delete your own comments.');
    }

    public function reply(Request $request, CommentModel $comment)
    {
        $request->validate([
            'content' => 'required|string|max:500',
        ]);

        $comment->replies()->create([
            'content' => $request->content,
            'user_id' => auth()->id(),
            'discussion_id' => $comment->discussion_id, // Ensure it's tied to the same discussion
        ]);

        return back()->with('message', 'Reply added successfully!');
    }
}
