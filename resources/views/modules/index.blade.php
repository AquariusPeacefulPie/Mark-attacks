<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Modules') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <script>
            function confirmDelete(url) {
                if (confirm('Êtes-vous sûr de vouloir supprimer ce module ?')) {
                    window.location.href = url;
                }
            }
        </script>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6" >
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        @if(session('success'))
                            <div class="" style="color: greenyellow" role="alert">
                                {{  session('success')  }}
                            </div>
                        @endif
                            <header>
                                @if(Auth::user()->role == 'admin')
                                <div class="flex items-center gap-4 float-right">
                                    <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('module.create') }}" id="createModule">
                                        {{ __('Créer un module') }}
                                    </a>
                                </div>
                                @endif

                                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                    {{ __('Liste des modules') }}
                                </h2>

                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    {{ __("Vous pouvez choisir un module ci-dessous") }}
                                </p>
                            </header>
                                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg " style="min-width: 150%;">
                                    <div class="p-6 text-gray-900 dark:text-gray-100 align-content-center " >
                                        <table class="table-auto" >
                                            <thead>
                                                <tr class="bg-gray-50 dark:bg-gray-700">
                                                    <th class="py-2 px-4 border-b">Module</th>
                                                    <th class="py-2 px-4 border-b">Coefficient</th>
                                                    <th class="py-2 px-4 border-b">Moyenne générale</th>
                                                    <th class="py-2 px-4 border-b">Moyenne (Vous)</th>
                                                    <th class="py-2 px-4 border-b">Professeur responsable</th>
                                                    <th class="py-2 px-4 border-b">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($modules as $module)
                                                <tr class="border-b">
                                                    <td class="py-2 text-center border-b">{{ $module->name }}</td>
                                                    <td class="py-2 text-center border-b">{{ $module->coefficient }}</td>
                                                    <td class="py-2 text-center border-b">{{ $module->average_mark }}</td>
                                                    <td class="py-2 text-center border-b">{{ $module->average_student }}</td>
                                                    @php
                                                        $user = App\Models\User::where('id',$module->user_id)->first()
                                                    @endphp
                                                    <td class="py-2 text-center border-b">{{ $user->lastname }} {{ $user->firstname }}</td>
                                                    <td class="py-2 text-center border-b">
                                                        <a href="{{ route('module.name.show', ['nommodule' => $module->name]) }}"
                                                            id="{{'seeMore'.$module->name}}"
                                                            class="text-blue-500 hover:underline">{{ __('Voir module') }}</a>
                                              @if(Auth::user()->role == 'admin')
                                                            <a href="#" class="text-red-500 hover:underline" onclick="confirmDelete('{{ route('module.delete', ['id' => $module->id]) }}')" style="display: inline-block" data-method="delete" id="{{'delete'.$module->name}}">
                                                                <i class="fas fa-trash-alt"></i> Supprimer
                                                            </a>
                                                            @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
