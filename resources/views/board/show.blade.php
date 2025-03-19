<div>
    <h1>Board Details</h1>

    <p><strong>Title:</strong> {{ $board->title }}</p>
    <p><strong>Description:</strong> {{ $board->description }}</p>

    @foreach($board->tags as $tag)
        <span>{{ $tag->name }}</span>
    @endforeach

    <p>Members: {{ $board->users->count() }}</p>

    <h3>Discussions:</h3>
    @if($board->discussion->isEmpty())
        <p>No discussions yet.</p>
    @else
        <ul>
            @foreach($board->discussion as $discussions)
                
                @if(!auth()->user()->isBlocking($discussions->user_id) && !$discussions->user->isBlocking(auth()->id()))
                    <li>
                        <p>{{ $discussions->content }}</p>
                        <small>Posted on {{ $discussions->created_at->format('M d, Y') }}</small>
                        <a href="{{ route('board.discussion.show', [$board->id, $discussions->id]) }}">View</a> | <a href="{{ route('board.discussion.edit', [$board->id, $discussions->id]) }}">Edit</a>

                        <form action="{{ route('discussion.like', $discussions->id) }}" method="POST">
                            @csrf
                            <button type="submit">
                                @if($discussions->likes->contains(auth()->user()->id))
                                    ❤️ Unlike
                                @else
                                    🤍 Like
                                @endif
                            </button>
                            <span>{{ $discussions->likes->count() }} {{ Str::plural('Like', $discussions->likes->count()) }}</span>
                        </form>

                        @if($discussions->user_id !== auth()->id())
                            @if(auth()->user()->isBlocking($discussions->user))
                                <form action="{{ route('user.block', $discussions->user->id) }}" method="POST">
                                    @csrf
                                    <button type="submit">Unblock</button>
                                </form>
                            @else
                                <form action="{{ route('user.block', $discussions->user->id) }}" method="POST">
                                    @csrf
                                    <button type="submit">Block</button>
                                </form>
                            @endif
                        @endif

                        <form action="{{ route('discussion.dislike', $discussions->id) }}" method="POST">
                            @csrf
                            <button type="submit">
                                @if($discussions->dislikes->contains(auth()->user()->id))
                                    👎 Undislike
                                @else
                                    👎 Dislike
                                @endif
                            </button>
                            <span>{{ $discussions->dislikes->count() }} {{ Str::plural('Dislike', $discussions->dislikes->count()) }}</span>
                        </form>
                        

                        <form action="{{ route('board.discussion.destroy', [$board->id, $discussions->id]) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button>Delete</button>
                        </form>
                    </li>
                @endif

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
</script>
