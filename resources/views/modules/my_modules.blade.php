<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Liste de mes modules') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ __('Choix du module') }}
                            </h2>

                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                {{ __("Vous pouvez choisir un module ci-dessous") }}
                            </p>
                        </header>
                        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                            <div class="bg-white dark:bg-gray-800  shadow-sm sm:rounded-lg">
                                <div class="p-6 text-gray-900 dark:text-gray-100">
                                    <table class="min-w-full border border-gray-300">
                                        <thead>
                                        <tr class="bg-gray-50 dark:bg-gray-700">
                                            <th class="py-2 px-4 border-b">Module</th>
                                            <th class="py-2 px-4 border-b">Coefficient</th>
                                            <th class="py-2 px-4 border-b">
                                            @if(Auth::user()->role == 'student')
                                            Ma Moyenne
                                            @endif
                                            </th>
                                            <th class="py-2 px-4 border-b">Moyenne de classe</th>
                                            <th class="py-2 px-4 border-b">Actions</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($modules as $module)
                                            <tr class="border-b">
                                                <td class="py-2 px-4 border-b">{{ $module->name }}</td>
                                                <td class="py-2 px-4 border-b">{{ $module->coefficient }}</td>
                                                <td class="py-2 px-4 border-b">
                                                @if(Auth::user()->role == 'student')
                                                {{ $module->my_average_mark}}
                                                @endif
                                                </td>
                                                <td class="py-2 px-4 border-b">{{ $module->average_mark }}</td>
                                                <td class="py-2 px-4 border-b">
                                                    <a href="{{ route('module.choisir', ['module_id' => $module->id]) }}"
                                                       class="text-blue-500 hover:underline">{{ __('Voir module') }}</a>
                                                </td>
                                            </tr>

                                            <tr class="bg-gray-50 dark:bg-gray-700">
                                                <th class="py-2 px-4 border-b"></th>
                                                <th class="py-2 px-4 border-b">Examen</th>
                                                <th class="py-2 px-4 border-b">
                                                @if(Auth::user()->role == 'student')
                                                Ma note
                                                @endif
                                                </th>
                                                <th class="py-2 px-4 border-b">Moyenne de classe</th>
                                                <th class="py-2 px-4 border-b"></th>
                                            </tr>
                                            @foreach($exams as $exam)
                                                @if($exam->module_id == $module->id)
                                                <tr>
                                                    <td class="py-2 px-4 border-b"></td>
                                                    <td class="py-2 px-4 border-b">{{ $exam->name }}</td>
                                                    <td class="py-2 px-4 border-b">
                                                    @if(Auth::user()->role == 'student')
                                                    {{ $exam->result }}
                                                    @endif
                                                    </td>
                                                    <td class="py-2 px-4 border-b">{{ $exam->average_result}}</td>
                                                    <td class="py-2 px-4 border-b"></td>
                                                </tr>
                                                @endif
                                            @endforeach

                                            <tr class="bg-gray-50 dark:bg-gray-700">
                                                <th>-------</th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
