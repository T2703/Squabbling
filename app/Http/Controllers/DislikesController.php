<?php

namespace App\Http\Controllers;

use App\Models\BoardModel;
use App\Models\Discussion;
use Illuminate\Http\Request;

class DislikesController extends Controller
{
    public function toggleDislike(BoardModel $board, Discussion $discussion)
    {
        $user = auth()->user();

        // Check if the user is not in the board or is not the owner
        if (!$board->users()->where('user_id', $user->id)->exists() && $board->user_id !== $user->id) {
            abort(403);
        }

        // Check if user has liked the discussion and remove it if true
        if ($discussion->likes()->where('user_id', $user->id)->exists()) {
            $discussion->likes()->detach($user->id);
        }

        // Toggle dislike
        if ($discussion->dislikes()->where('user_id', $user->id)->exists()) {
            $discussion->dislikes()->detach($user->id); // Remove dislike
            return back()->with('message', 'You removed your dislike.');
        } else {
            $discussion->dislikes()->attach($user->id); // Add dislike
            return back()->with('message', 'You disliked the discussion.');
        }
    }
}
