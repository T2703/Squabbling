<?php

namespace App\Http\Controllers;

use App\Models\Discussion;
use Illuminate\Http\Request;

class DislikesController extends Controller
{
    public function toggleDislike(Discussion $discussion)
    {
        $user = auth()->user();

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
