<div>

    <h1>{{ $discussion->content }}</h1>

    <a href="{{ route('board.index') }}">Back to Boards</a>
    
    @if($board->users->contains(auth()->user()->id) || $board->user_id === auth()->id())
        <button type="button" onclick="openModal()">Comment</button>
    @endif

    @if($discussion->comments->isEmpty())
        <p>No comments yet.</p>
    @else
        <ul>
            @foreach($discussion->comments as $comment)
                <li>
                    <p>{{ $comment->content }}</p>
                    @if ($comment->user_id === $discussion->user_id)
                        <small>
                            By OP
                            on {{ $comment->created_at->format('M d, Y') }}
                        </small> 
                    @endif

                    <form action="{{ route('comment.like', $comment->id) }}" method="POST">
                        @csrf
                        <button type="submit">
                            @if(($comment->likes ?? collect())->contains(auth()->user()->id))
                                ❤️ Unlike
                            @else
                                🤍 Like
                            @endif
                        </button>
                        <span>{{ ($comment->likes ?? collect())->count() }} {{ Str::plural('Like', ($comment->likes ?? collect())->count()) }}</span>
                    </form>

                    <form action="{{ route('comment.dislike', $comment->id) }}" method="POST">
                        @csrf
                        <button type="submit">
                            @if($comment->dislikes->contains(auth()->user()->id))
                                👎 Undislike
                            @else
                                👎 Dislike
                            @endif
                        </button>
                        <span>{{ $comment->dislikes->count() }} {{ Str::plural('Dislike', $comment->dislikes->count()) }}</span>
                    </form>
                    
                    <button type="button" onclick="openModalComment({{ $comment->id }})">Reply</button>

                    <!-- Reply Modal for Each Comment -->
                    <div id="commentModal-{{ $comment->id }}" class="modal" style="display:none;">
                        <div class="modal-content">
                            <button class="close-button" onclick="closeModalComment({{ $comment->id }})">&times;</button>
                            <h2>Add a Reply</h2>
                            <form action="{{ route('comment.reply', $comment->id) }}" method="POST">
                                @csrf
                                <label for="content">Reply Content:</label>
                                <textarea id="content" name="content" rows="3" required></textarea>
                                <button type="submit" style="margin-top: 10px;">Add Reply</button>
                            </form>
                        </div>
                    </div>

                    <!-- Display Replies with Toggle -->
                    @if($comment->replies->isNotEmpty())
                        <button type="button" onclick="toggleReplies({{ $comment->id }})">
                            View Replies ({{ $comment->replies->count() }})
                        </button>

                        <div id="replies-{{ $comment->id }}" style="display: none; margin-left: 20px;">
                            @foreach($comment->replies as $reply)
                                <p>{{ $reply->content }}</p>
                                <small>By OP on {{ $reply->created_at->format('M d, Y') }}</small>
                                <button type="button" onclick="openModalComment({{ $reply->id }})">Reply</button>

                                @if($reply->user_id === auth()->id())
                                    <form action="{{ route('comment.destroy', $reply->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit">Delete</button>
                                    </form>
                                @endif
                            @endforeach
                        </div>
                    @endif
                    @if($comment->user_id === auth()->id())
                        <form action="{{ route('comment.destroy', $comment->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Delete</button>
                        </form>
                    @endif
                </li>
            @endforeach
        </ul>
    @endif

    <!-- Add Discussion Modal -->
    <div id="discussionModal" class="modal" style="display:none;">
        <div class="modal-content">
            <button class="close-button" onclick="closeModal()">&times;</button>
            <h2>Add a Comment</h2>
            <form action="{{ route('comment.store', $discussion->id) }}" method="POST">
                @csrf
                <label for="content">Discussion Content:</label>
                <textarea id="content" name="content" rows="3" required></textarea>
                <button type="submit" style="margin-top: 10px;">Add Discussion</button>
            </form>
        </div>
    </div>

</div>

<script>
    function openModal() {
        document.getElementById('discussionModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('discussionModal').style.display = 'none';
    }

    // Close modal if user clicks outside content area
    window.onclick = function(event) {
        const modal = document.getElementById('discussionModal');
        if (event.target === modal) {
            closeModal();
        }
    }

    function openModalComment(commentId) {
        document.getElementById(`commentModal-${commentId}`).style.display = 'flex';
    }

    function closeModalComment(commentId) {
        document.getElementById(`commentModal-${commentId}`).style.display = 'none';
    }


    // Close modal if user clicks outside content area
    window.onclick = function(event) {
        const discussionModal = document.getElementById('discussionModal');
        const commentModals = document.querySelectorAll('[id^="commentModal-"]');

        if (event.target === discussionModal) {
            closeModal();
        }

        commentModals.forEach(modal => {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
    }
</script>

<script>
    function toggleReplies(commentId) {
        let repliesDiv = document.getElementById(`replies-${commentId}`);
        if (repliesDiv.style.display === "none") {
            repliesDiv.style.display = "block";
        } else {
            repliesDiv.style.display = "none";
        }
    }
</script>