<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Gestion des utilisateurs') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <script>
            function confirmDelete(url) {
                if (confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) {
                    window.location.href = url;
                }
            }
            function confirmActivation(url) {
                if (confirm('Êtes-vous sûr de vouloir activer le compte de cet utilisateur ?')) {
                    window.location.href = url;
                }
            }
        </script>


        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="flex justify-center mb-4">
                        @php
                            $categories = ['users','students', 'teachers', 'admins','inactive users'];
                        @endphp
                        @foreach($categories as $category)
                            @if($category === 'users')
                                <a href="{{ route('liste-utilisateurs', ['type' => $category]) }}" id="filterUsers" class="mx-4 px-4 py-2 bg-blue-500 text-white rounded-full @if($selectedCategory == $category) bg-green-500 @endif">{{ 'Utilisateurs' }}</a>
                            @elseif($category === 'students')
                                <a href="{{ route('liste-utilisateurs', ['type' => $category]) }}" id="filterStudents" class="mx-4 px-4 py-2 bg-blue-500 text-white rounded-full @if($selectedCategory == $category) bg-green-500 @endif">{{ 'Etudiants' }}</a>
                            @elseif($category === 'teachers')
                                <a href="{{ route('liste-utilisateurs', ['type' => $category]) }}" id="filterTeachers" class="mx-4 px-4 py-2 bg-blue-500 text-white rounded-full @if($selectedCategory == $category) bg-green-500 @endif">{{ 'Enseignants' }}</a>
                            @elseif($category === 'admins')
                                <a href="{{ route('liste-utilisateurs', ['type' => $category]) }}" id="filterAdmins" class="mx-4 px-4 py-2 bg-blue-500 text-white rounded-full @if($selectedCategory == $category) bg-green-500 @endif">{{ 'Admins' }}</a>
                            @elseif($category === 'inactive users')
                                <a href="{{ route('liste-utilisateurs', ['type' => $category]) }}" id="filterInactiveUsers" class="mx-4 px-4 py-2 bg-blue-500 text-white rounded-full @if($selectedCategory == $category) bg-green-500 @endif">{{ 'Utilisateurs inactifs' }}</a>
                            @endif
                        @endforeach
                    </div>

                    <table class="min-w-full border border-gray-300">
                        <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700">
                            <th class="py-2 px-4 border-b">Nom</th>
                            <th class="py-2 px-4 border-b">Prénom</th>
                            <th class="py-2 px-4 border-b">Date de naissance</th>
                            <th class="py-2 px-4 border-b">Email</th>
                            <th class="py-2 px-4 border-b">Créé le</th>
                            @if($selectedCategory != 'users' && $selectedCategory != 'admins')
                                <th class="py-2 px-4 border-b">Actif</th>
                            @endif
                            <th class="py-2 px-4 border-b">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($users as $user)
                            <tr class="border-b">
                                <td class="py-2 px-4 border-b">@if($user->role) {{ $user->firstname}} @else {{ $user->user->firstname}} @endif</td>
                                <td class="py-2 px-4 border-b">@if($user->role) {{ $user->lastname}} @else {{ $user->user->lastname}} @endif</td>
                                <td class="py-2 px-4 border-b">@if($user->role) {{ $user->birthdate}} @else {{ $user->user->birthdate}} @endif</td>
                                <td class="py-2 px-4 border-b">@if($user->role) {{ $user->email}} @else {{ $user->user->email}} @endif</td>
                                <td class="py-2 px-4 border-b">@if($user->role) {{ $user->created_at}} @else {{ $user->user->created_at}} @endif</td>
                                @if($selectedCategory != 'users' && $selectedCategory != 'admins')
                                    <td class="py-2 px-4 border-b">{{ $user->active ? 'Oui' : 'Non' }}</td>
                                @endif
                                <td class="py-2 px-4 border-b">
                                    <a id="update{{$user->id}}" href="{{ route('liste-utilisateurs.edit', ['id' => $user->id, 'type' => $selectedCategory]) }}" class="text-blue-500 hover:underline mr-2">
                                        <i class="fas fa-edit"></i> Modifier
                                    </a>
                                    @if($selectedCategory != 'admins' && $selectedCategory != 'users' &&  $user->active==0 )
                                        <a id="{{'activate'.$user->id}}" href="#" onclick="confirmActivation(`{{ route('liste-utilisateurs.activate', ['id' => $user->id, 'type' => $selectedCategory]) }}`)" class="text-green-500 hover:underline mr-2">
                                            <i class="fas fa-check-circle"></i> Activer
                                        </a>
                                    @endif
                                    <a id="{{'delete'.$user->id}}" href="#" class="text-red-500 hover:underline" onclick="confirmDelete(`{{ route('liste-utilisateurs.delete', ['id' => $user->id, 'type' => $selectedCategory]) }}`)">
                                        <i class="fas fa-trash-alt"></i> Supprimer
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        @if($selectedCategory != 'users' && $selectedCategory != 'inactive users')
                            <tr class="border-b">
                                <td colspan="7" class="py-2 px-4 border-b">
                                    @php
                                        $nameCategoryFrench = ''
                                    @endphp
                                    @if($selectedCategory === 'students')
                                        @php
                                            $nameCategoryFrench = 'Etudiant'
                                        @endphp
                                    @elseif($selectedCategory === 'teachers')
                                        @php
                                            $nameCategoryFrench = 'Enseignant'
                                        @endphp
                                    @elseif($selectedCategory === 'admins')
                                        @php
                                            $nameCategoryFrench = 'Admin'
                                        @endphp
                                    @endif
                                    <a href="{{ route('admin.create', ['category' => $selectedCategory]) }}" id="addNewUser" class="block w-full py-2 bg-blue-600 text-white text-center rounded-full hover:bg-blue-700">Ajouter {{ $nameCategoryFrench }}</a>

                                </td>
                            </tr>
                        @endif
                        </tbody>
                    </table>

                    @if($selectedCategory != "inactive users")
                        <!-- Affichage des liens de pagination -->
                        {{ $users->links('pagination.custom') }}
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
