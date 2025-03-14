<x-app-layout>
    <h1>Boards List</h1>

    @if(session('message'))
        <p style="color: green;">{{ session('message') }}</p>
    @endif

    @if($boards->isEmpty())
        <p>No boards available.</p>
    @else
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
                </li>
                <hr>
            @endforeach
        </ul>
    @endif
</x-app-layout>
