<x-app-layout>

    <form action="{{ route('board.search') }}" method="GET">
        <input type="text" name="search" placeholder="Search Boards">
        <button type="submit">Search</button>
    </form>

    <h1>Boards List</h1>

    @if(session('message'))
        <p style="color: green;">{{ session('message') }}</p>
    @endif

    @if(isset($boards) && $boards->isNotEmpty())
        <ul>
            @foreach($boards as $board)
                <li>
                    <strong>{{ $board->title }}</strong><br>
                    {{ $board->description ?? 'No description available' }}<br>

                    <!-- View Board Link -->
                    <a href="{{ route('board.show', $board) }}">View</a> | 
                    
                    <!-- Edit Board Link -->
                    <a href="{{ route('board.edit', $board) }}">Edit</a>
                    <form action="{{ route('board.destroy', $board) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button>Delete</button>
                    </form>

                    <form action="{{ route('board.join', $board->id) }}" method="POST">
                        @csrf
                        <button type="submit">Join Board</button>
                    </form>
                </li>
                <hr>
            @endforeach
        </ul>
    @else
        <p>No boards available.</p>
    @endif
</x-app-layout>
