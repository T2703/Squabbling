<?php

namespace App\Http\Controllers;

use App\Models\CommentModel;
use Illuminate\Http\Request;

class CommentDislikeController extends Controller
{
    public function toggleDislike(CommentModel $comment)
    {
        $user = auth()->user();

        // Check if user has liked the comment and remove it if true
        if ($comment->likes()->where('user_id', $user->id)->exists()) {
            $comment->likes()->detach($user->id);
        }

        // Toggle dislike
        if ($comment->dislikes()->where('user_id', $user->id)->exists()) {
            $comment->dislikes()->detach($user->id); // Remove dislike
            return back()->with('message', 'You removed your dislike.');
        } else {
            $comment->dislikes()->attach($user->id); // Add dislike
            return back()->with('message', 'You disliked the comment.');
        }
    }
}
