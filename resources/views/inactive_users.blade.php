<div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow-lg mb-6">
    <h2 class="text-2xl font-semibold mb-4 text-gray-900 dark:text-white">Demande d'inscription</h2>

    @if($inactiveUsers->isEmpty())
        <p class="text-gray-700 dark:text-gray-300">Il n'y a pas de demandes d'inscription en cours.</p>
    @else
        <div class="max-w-full overflow-x-auto">
            <div class="grid grid-cols-3 gap-4">
                @foreach ($inactiveUsers->sortByDesc('created_at') as $user)
                    <div class="bg-white dark:bg-cyan-700 p-6 rounded-xl shadow-lg">
                        <h3 class="text-2xl font-semibold mb-2 text-gray-900 dark:text-white">{{ $user->firstname }} {{ $user->lastname }} - {{ $user->role }}</h3>
                        <p class="text-gray-700 dark:text-gray-300">{{ $user->email }}</p>
                        <p class="text-gray-700 dark:text-gray-300">Date de naissance : {{ $user->birthdate }}</p>
                        {{-- Ajoutez d'autres informations que vous souhaitez afficher --}}
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
