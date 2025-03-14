<x-app-layout>
    <h1>Create a Discussion</h1>

    @if(session('message'))
        <p style="color: green;">{{ session('message') }}</p>
    @endif

    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('board.discussion.update', [$board->id, $discussion->id]) }}" method="POST">
        @csrf
        @method('PUT')

        <label for="content">Discussion Content:</label><br>
        <textarea id="content" name="content" rows="4" required>{{ $discussion->content }}</textarea><br><br>
        <button type="submit">Create Discussion</button>
    </form>
</x-app-layout>
