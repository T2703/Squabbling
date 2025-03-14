<x-app-layout>
    <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">

    <h1>Create a New Board</h1>

    @if(session('message'))
        <p style="color: green;">{{ session('message') }}</p>
    @endif

    <form action="{{ route('board.store') }}" method="POST">
        @csrf
        <label for="title">Title:</label><br>
        <input type="text" id="title" name="title" required><br><br>

        <label for="description">Description:</label><br>
        <textarea id="description" name="description" rows="4" ></textarea><br><br>

        <label for="tags">Tags (10 only):</label>
        <input name="tags" id="tags" placeholder="e.g. Laravel, PHP, Web" />

        <button type="submit">Create Board</button>
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
