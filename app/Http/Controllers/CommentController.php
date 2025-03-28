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

        $user = auth()->user();

        // Check if user already has a reply_number in this discussion
        $existingReply = CommentModel::where('discussion_id', $comment->discussion_id)
                                ->where('user_id', $user->id)
                                ->first();
        
        // Placeholder if the replier is the owner.
        $replyNumber = NULL;
        if ($comment->user_id !== auth()->id()) {
            $maxReplyNumber = CommentModel::where('discussion_id', $comment->discussion_id)->max('reply_number');
            $replyNumber = $existingReply ? $existingReply->reply_number : ($maxReplyNumber !== null ? $maxReplyNumber + 1 : 1);
        }
        
        $comment->replies()->create([
            'content' => $request->content,
            'user_id' => auth()->id(),
            'discussion_id' => $comment->discussion_id, // Ensure it's tied to the same discussion
            'reply_number' => $replyNumber,
        ]);

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
