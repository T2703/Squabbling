<?php

namespace App\Http\Controllers;

use App\Models\BoardModel;
use App\Models\Discussion;
use Illuminate\Http\Request;

class LikesController extends Controller
{
    public function toggleLike(BoardModel $board, Discussion $discussion)
    {
        $user = auth()->user();

        // Check if the user is not in the board or is not the owner
        if (!$board->users()->where('user_id', $user->id)->exists() && $board->user_id !== $user->id) {
            abort(403);
        }

        // Check if user has disliked the discussion and remove it if true
        if ($discussion->dislikes()->where('user_id', $user->id)->exists()) {
            $discussion->dislikes()->detach($user->id);
        }

        if ($discussion->likes()->where('user_id', $user->id)->exists()) {
            // Unlike
            $discussion->likes()->detach($user->id);
            return back()->with('message', 'You unliked the discussion.');
        } else {
            // Like
            $discussion->likes()->attach($user->id);
            return back()->with('message', 'You liked the discussion.');
        }
    }
}
