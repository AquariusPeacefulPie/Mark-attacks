<div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow-lg mb-6">
    <h2 class="text-2xl font-semibold mb-4 text-gray-900 dark:text-white">Modules inscrits</h2>

    @if($studentModules->isEmpty())
        <p class="text-gray-700 dark:text-gray-300">L'étudiant n'est inscrit dans aucun module pour le moment.</p>
    @else
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
            @foreach ($studentModules as $studentModule)
                <div class="bg-white dark:bg-cyan-700 p-6 rounded-xl shadow-lg">
                    <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-white">{{ $studentModule->module->name }}</h3>
                    <p class="text-gray-700 dark:text-gray-300">{{ $studentModule->module->description }}</p>
                </div>
            @endforeach
        </div>
    @endif
</div>
