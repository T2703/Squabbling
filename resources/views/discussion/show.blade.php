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
                    <button type="button" onclick="openModalEditComment({{ $comment->id }})">Edit</button>

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

                    <!-- Edit Modal for Each Comment -->
                    <div id="editModal-{{ $comment->id }}" class="modal" style="display:none;">
                        <div class="modal-content">
                            <button class="close-button" onclick="closeModalEditComment({{ $comment->id }})">&times;</button>
                            <h2>Edit Reply</h2>
                            <form action="{{ route('comment.update', $comment->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <label for="content">Reply Content:</label>
                                <textarea id="content" name="content" rows="3" required>{{ $comment->content }}</textarea>
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

                                <!-- Reply Modal for Each Reply This is needed otherwise it can't work :(  -->
                                <div id="commentModal-{{ $reply->id }}" class="modal" style="display:none;">
                                    <div class="modal-content">
                                        <button class="close-button" onclick="closeModalComment({{ $reply->id }})">&times;</button>
                                        <h2>Add a Reply</h2>
                                        <form action="{{ route('comment.reply', $reply->id) }}" method="POST">
                                            @csrf
                                            <label for="content">Reply Content:</label>
                                            <textarea id="content" name="content" rows="3" required></textarea>
                                            <button type="submit" style="margin-top: 10px;">Add Reply</button>
                                        </form>
                                    </div>
                                </div>
                                <p>{{ $reply->content }}</p>
                                @if ($reply->user_id === $discussion->user_id)
                                    <small>
                                        By OP
                                        on {{ $comment->created_at->format('M d, Y') }}
                                    </small> 
                                @else
                                    <strong>#{{ $reply->reply_number }} ▸ #{{ $comment->reply_number }}</strong> 
                                @endif

                                <form action="{{ route('comment.like', $reply->id) }}" method="POST">
                                    @csrf
                                    <button type="submit">
                                        @if(($reply->likes ?? collect())->contains(auth()->user()->id))
                                            ❤️ Unlike
                                        @else
                                            🤍 Like
                                        @endif
                                    </button>
                                    <span>{{ ($reply->likes ?? collect())->count() }} {{ Str::plural('Like', ($reply->likes ?? collect())->count()) }}</span>
                                </form>
            
                                <form action="{{ route('comment.dislike', $reply->id) }}" method="POST">
                                    @csrf
                                    <button type="submit">
                                        @if($reply->dislikes->contains(auth()->user()->id))
                                            👎 Undislike
                                        @else
                                            👎 Dislike
                                        @endif
                                    </button>
                                    <span>{{ $reply->dislikes->count() }} {{ Str::plural('Dislike', $reply->dislikes->count()) }}</span>
                                </form>

                                <button type="button" onclick="openModalComment({{ $reply->id }})">Reply</button>

                                <!-- Reply for the replies. -->
                                @if($reply->replies->isNotEmpty())
                                    <button type="button" onclick="toggleReplies({{ $reply->id }})">
                                        View Replies ({{ $reply->replies->count() }})
                                    </button>
                                    <div id="replies-{{ $reply->id }}" style="display: none; margin-left: 40px;">
                                        @foreach($reply->replies as $nestedReply)

                                            <!-- Reply Modal for Each Reply This is needed otherwise it can't work :(  -->
                                            <div id="commentModal-{{ $nestedReply->id }}" class="modal" style="display:none;">
                                                <div class="modal-content">
                                                    <button class="close-button" onclick="closeModalComment({{ $nestedReply->id }})">&times;</button>
                                                    <h2>Add a Reply</h2>
                                                    <form action="{{ route('comment.reply', $nestedReply->id) }}" method="POST">
                                                        @csrf
                                                        <label for="content">Reply Content:</label>
                                                        <textarea id="content" name="content" rows="3" required></textarea>
                                                        <button type="submit" style="margin-top: 10px;">Add Reply</button>
                                                    </form>
                                                </div>
                                            </div>

                                            <form action="{{ route('comment.like', $nestedReply->id) }}" method="POST">
                                                @csrf
                                                <button type="submit">
                                                    @if(($nestedReply->likes ?? collect())->contains(auth()->user()->id))
                                                        ❤️ Unlike
                                                    @else
                                                        🤍 Like
                                                    @endif
                                                </button>
                                                <span>{{ ($nestedReply->likes ?? collect())->count() }} {{ Str::plural('Like', ($nestedReply->likes ?? collect())->count()) }}</span>
                                            </form>
                        
                                            <form action="{{ route('comment.dislike', $nestedReply->id) }}" method="POST">
                                                @csrf
                                                <button type="submit">
                                                    @if($nestedReply->dislikes->contains(auth()->user()->id))
                                                        👎 Undislike
                                                    @else
                                                        👎 Dislike
                                                    @endif
                                                </button>
                                                <span>{{ $nestedReply->dislikes->count() }} {{ Str::plural('Dislike', $nestedReply->dislikes->count()) }}</span>
                                            </form>
            

                                            <p><strong>#{{ $nestedReply->reply_number }} ▸ #{{ $reply->reply_number }}</strong> {{ $nestedReply->content }}</p>
                                            <button type="button" onclick="openModalComment({{ $nestedReply->id }})">Reply</button>
                                        @endforeach
                                    </div>
                                @endif

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
        let modal = document.getElementById(`commentModal-${commentId}`);
        if (modal) {
            modal.style.display = 'flex';
        } else {
            console.error(`Modal with ID commentModal-${commentId} not found.`);
        }
    }

    function closeModalComment(commentId) {
        let modal = document.getElementById(`commentModal-${commentId}`);
        if (modal) {
            modal.style.display = 'none';
        }
    }

    function openModalEditComment(commentId) {
        let modal = document.getElementById(`editModal-${commentId}`);
        if (modal) {
            modal.style.display = 'flex';
        } else {
            console.error(`Modal with ID editModal-${commentId} not found.`);
        }
    }

    function closeModalEditComment(commentId) {
        let modal = document.getElementById(`editModal-${commentId}`);
        if (modal) {
            modal.style.display = 'none';
        }
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
