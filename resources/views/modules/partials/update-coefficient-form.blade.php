<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Informations du module') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Vous pouvez voir les informations ci-dessous") }}
        </p>
    </header>
    @if(auth()->user()->role != 'student')
    <form method="post" action="{{ route('module.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <input type="hidden" name="module_id" value="{{ $module->id }}">
        <div>
            <x-input-label for="name" :value="__('Nom')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $module->name)" required autofocus autocomplete="name"/>
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="coefficient" :value="__('Coefficient')" />
            <x-text-input id="coefficient" name="coefficient" type="text" class="mt-1 block w-full" :value="old('coefficient', $module->coefficient)" required autofocus autocomplete="coefficient" />
            <x-input-error class="mt-2" :messages="$errors->get('coefficient')" />
        </div>

        <p class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Note moyenne : {{ $module->average_mark }}</p>

        <div class="flex items-center gap-4">
            <x-primary-button id="submitModuleUpdate">{{ __('Sauvegarder') }}</x-primary-button>
        </div>
    </form>
    @else

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <table class="min-w-full border border-gray-300">
                        <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700">
                            <th class="py-2 px-4 border-b">Nom module</th>
                            <th class="py-2 px-4 border-b">Coefficient</th>
                            <th class="py-2 px-4 border-b">Moyenne générale</th>
                            @if(isset($averageMark))
                                @if($averageMark != 0 && $averageMark != "---")
                                    <th class="py-2 px-4 border-b">Votre note moyenne</th>
                                @endif
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                                <tr class="border-b">
                                    <td class="py-2 px-4 border-b text-center">{{ $module->name }}</td>
                                    <td class="py-2 px-4 border-b text-center">{{ $module->coefficient }}</td>
                                    <td class="py-2 px-4 border-b text-center"> {{$module->average_mark}}</td>
                                    @if(isset($averageMark))
                                        @if($averageMark != 0 && $averageMark != "---")
                                    <td class="py-2 px-4 border-b text-center"> {{$averageMark}}</td>
                                        @endif
                                    @endif
                                </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</section>
