<div>
    <h1>Blocks</h1>

    <p> {{ auth()->user()->getBlockers(); }}</p>

    <form action="{{ route('blocks.unblockAll') }}" method="POST" style="margin-top: 20px;">
        @csrf
        <button type="submit" onclick="return confirm('Are you sure you want to unblock all users?')">
            Unblock All
        </button>
    </form>
</div>
