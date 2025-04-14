<?php

namespace App\Http\Controllers;

use App\Models\CommentModel;
use App\Models\Discussion;
use App\Notifications\CommentOnDiscussion;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Discussion $discussion)
    {
        $request->validate([
            'content' => 'required|string|max:500',
        ]);

        $user = auth()->user();

        // Check if user already has a comment_number in this discussion
        $existingComment = CommentModel::where('discussion_id', $discussion->id)
                            ->where('user_id', $user->id)
                            ->orderBy('comment_number', 'desc')
                            ->first();

        $nextCommentNumber = $existingComment ? $existingComment->comment_number + 1 : 1;
        
        $comment = $discussion->comments()->create([
            'content' => $request->content,
            'user_id' => auth()->id(),
            'reply_number' => $nextCommentNumber,
        ]);

        // Load discussion
        $comment->load('discussion');

        // Only notify if the commenter isn't the owner of the discussion
        if ($discussion->user_id !== auth()->id()) {
            $discussion->user->notify(new CommentOnDiscussion($comment));
        }

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
    
        $user = auth()->user();
    
        // Check for existing reply from this user to this parent comment
        $existingReply = $comment->replies()
            ->where('user_id', $user->id)
            ->first();
    
        if ($existingReply) {
            $replyNumber = $existingReply->reply_number;
        } else {
            // Generate a unique random reply number within this comment's scope
            do {
                $replyNumber = random_int(1000, 9999); // or any range you prefer
            } while ($comment->replies()->where('reply_number', $replyNumber)->exists());
        }

        $reply = $comment->replies()->create([
            'content' => $request->content,
            'user_id' => $user->id,
            'discussion_id' => $comment->discussion_id,
            'reply_number' => $replyNumber,
        ]);

        // Load discussion
        $reply->load('discussion');

        // Only notify if the commenter isn't the owner of the discussion
        if ($comment->user_id !== auth()->id()) {
            $comment->user->notify(new CommentOnDiscussion($reply));
        }
    
        return back()->with('message', 'Reply added successfully!');
    }

    /**
     * Update the specified discussion.
     */
    public function update(Request $request, CommentModel $comment)
    {
        if ($comment->user_id !== request()->user()->id) {
            abort(403);
        }

        $data = $request->validate([
            'content' => ['required', 'string'],
        ]);

        $comment->update($data);

        return back()->with('message', 'Comment edited successfully!');
    }


}
