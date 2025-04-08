<div>
    <h1>Board Details</h1>

    <p><strong>Title:</strong> {{ $board->title }}</p>
    <p><strong>Description:</strong> {{ $board->description }}</p>

    @foreach($board->tags as $tag)
        <span>{{ $tag->name }}</span>
    @endforeach

    <p>Members: {{ $board->users->count() }}</p>

    <h3>Discussions:</h3>
    @if($discussions->isEmpty())
        <p>No discussions yet.</p>
    @else
        <ul>
            @foreach($discussions as $discussion)
                @php
                    $isBlocked = auth()->user()->isBlocking($discussion->user_id) || $discussion->user->isBlocking(auth()->id());
                @endphp

                @if ($isBlocked)
                    <button onclick="toggleBlockedDiscussion({{ $discussion->id }}, this)">
                        Show Blocked Discussion
                    </button>
                    <li id="discussion-{{ $discussion->id }}" style="display: none;">
                @else
                    <li id="discussion-{{ $discussion->id }}">
                @endif

                    <p>{{ $discussion->content }}</p>
                    <small>Posted on {{ $discussion->created_at->format('M d, Y') }}</small>
                    <a href="{{ route('board.discussion.show', [$board->id, $discussion->id]) }}">View</a> | 
                    <a href="{{ route('board.discussion.edit', [$board->id, $discussion->id]) }}">Edit</a>

                    <form action="{{ route('discussion.like', [$board->id, $discussion->id]) }}" method="POST">
                        @csrf
                        <button type="submit">
                            @if($discussion->likes->contains(auth()->user()->id))
                                ❤️ Unlike
                            @else
                                🤍 Like
                            @endif
                        </button>
                        <span>{{ $discussion->likes->count() }} {{ Str::plural('Like', $discussion->likes->count()) }}</span>
                    </form>

                    @if($discussion->user_id !== auth()->id())
                        <form action="{{ route('user.block', $discussion->user->id) }}" method="POST">
                            @csrf
                            <button type="submit">
                                {{ auth()->user()->isBlocking($discussion->user) ? 'Unblock' : 'Block' }}
                            </button>
                        </form>
                    @endif

                    <form action="{{ route('discussion.dislike', [$board->id, $discussion->id]) }}" method="POST">
                        @csrf
                        <button type="submit">
                            @if($discussion->dislikes->contains(auth()->user()->id))
                                👎 Undislike
                            @else
                                👎 Dislike
                            @endif
                        </button>
                        <span>{{ $discussion->dislikes->count() }} {{ Str::plural('Dislike', $discussion->dislikes->count()) }}</span>
                    </form>

                    @if($discussion->user_id === auth()->id()|| $board->user_id === auth()->id())
                        <form action="{{ route('board.discussion.destroy', [$board->id, $discussion->id]) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button>Delete</button>
                        </form>
                    @endif

                    @if($board->user_id === auth()->id())
                        <form action="{{ route('board.kick', $board->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $discussion->user->id }}">
                            <button type="submit">Kick</button>
                        </form>
                    @endif

                </li>
            @endforeach
        </ul>
    @endif

    <a href="{{ route('board.index') }}">Back to Boards</a>

    @if($board->users->contains(auth()->user()->id) || $board->user_id === auth()->id())
        <button type="button" onclick="openModal()">Add Discussion</button>
    @endif

    @if($board->users->contains(auth()->user()->id))
        <form action="{{ route('board.leave', $board->id) }}" method="POST">
            @csrf
            <button type="submit">Leave Board</button>
        </form>
    @endif

    <!-- Add Discussion Modal -->
    <div id="discussionModal" class="modal" style="display:none;">
        <div class="modal-content">
            <button class="close-button" onclick="closeModal()">&times;</button>
            <h2>Add a Discussion</h2>
            <form action="{{ route('board.discussion.store', $board->id) }}" method="POST">
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

    function toggleBlockedDiscussion(discussionId, button) {
        const discussionElement = document.getElementById(`discussion-${discussionId}`);

        if (discussionElement.style.display === "none") {
            discussionElement.style.display = "block";
            button.textContent = "Hide Blocked Discussion";
        } else {
            discussionElement.style.display = "none";
            button.textContent = "Show Blocked Discussion";
        }
    }
</script>

<a href="{{ route('board.showPopular', $board->id) }}">Sort by Most Popular</a>
