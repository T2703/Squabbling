<x-app-layout>
    <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">

    <h1>Edit Board</h1>

    @if(session('message'))
        <p style="color: green;">{{ session('message') }}</p>
    @endif

    <form action="{{ route('board.update', $board) }}" method="POST">
        @csrf
        @method('PUT')

        <label for="title">Title:</label><br>
        <input type="text" id="title" name="title" value="{{ $board->title }}" required><br>
        @error('title')
            <p style="color: red;">{{ $message }}</p>
        @enderror
        <br>

        <label for="description">Description:</label><br>
        <textarea id="description" name="description" rows="4">{{ $board->description }}</textarea><br>
        @error('description')
            <p style="color: red;">{{ $message }}</p>
        @enderror
        <br>

        <label for="tags">Tags (10 only):</label>
        <input name="tags" id="tags" value="{{ implode(',', $board->tags->pluck('name')->toArray()) }}" placeholder="e.g. Laravel, PHP, Web" />
        @error('description')
        <p style="color: red;">{{ $message }}</p>
        @enderror
        <br>

        <button type="submit">Submit</button>
    </form>
</x-app-layout>
<script>
    var input = document.querySelector('#tags');
    new Tagify(input, {
        whitelist: [], 
        maxTags: 10,   
        dropdown: {
            maxItems: 10,
            classname: "tags-look", 
            enabled: 0,
            closeOnSelect: false
        }
    });
</script>
