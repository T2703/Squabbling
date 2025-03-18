<?php

namespace App\Http\Controllers;

use App\Models\Discussion;
use Illuminate\Http\Request;

class LikesController extends Controller
{
    public function toggleLike(Discussion $discussion)
    {
        $user = auth()->user();

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
