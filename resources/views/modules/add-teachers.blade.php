<x-guest-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Module '.$nommodule) }}
        </h2>
    </x-slot>
    <div class="container mx-auto">
        <div class="py-8">
            <h1 class="text-3xl font-semibold">Ajouter enseignant(s):</h1>

            <!-- Search Input -->
            <div class="mb-4">
                <input type="text" id="search" class="form-control" placeholder="Search">
            </div>

            <!-- List -->
            <form action="{{ route('module.name.assign', ['nommodule' => $module->name, 'type' => 'teacher']) }}" method="POST">
                @csrf
                <ul class="list-group" id="searchableList">
                @foreach($teachers as $teacher)
                    <li class="list-group-item py-2">
                        <input id="checkBoxAdd{{$teacher->id}}" type="checkbox" name="selected_teachers[]" value="{{ $teacher->id }}">
                        {{ $teacher->firstname }}, {{ $teacher->lastname }}
                    </li>
                @endforeach
                </ul>

                <button id="submitNewTeacher" type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Ajouter au module</button>
            </form>
        </div>
    </div>
</x-guest-layout>

<script>
    // JavaScript to make the list searchable
    const searchInput = document.getElementById('search');
    const list = document.getElementById('searchableList').getElementsByTagName('li');

    searchInput.addEventListener('keyup', function() {
        const filter = searchInput.value.toLowerCase();

        for (let i = 0; i < list.length; i++) {
            const item = list[i];
            const text = item.textContent.toLowerCase();

            if (text.includes(filter)) {
                item.style.display = 'list-item';
            } else {
                item.style.display = 'none';
            }
        }
    });
</script>
