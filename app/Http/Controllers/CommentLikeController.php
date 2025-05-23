<?php

namespace App\Http\Controllers;

use App\Models\CommentModel;
use Illuminate\Http\Request;

class CommentLikeController extends Controller
{
    public function toggleLike(CommentModel $comment)
    {
        $user = auth()->user();

        // Check if user has disliked the comment and remove it if true
        if ($comment->dislikes()->where('user_id', $user->id)->exists()) {
            $comment->dislikes()->detach($user->id);
        }

        if ($comment->likes()->where('user_id', $user->id)->exists()) {
            // Unlike
            $comment->likes()->detach($user->id);
            return back()->with('message', 'You unliked the comment.');
        } else {
            // Like
            $comment->likes()->attach($user->id);
            return back()->with('message', 'You liked the comment.');
        }
    }
}
