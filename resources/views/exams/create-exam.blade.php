<x-guest-layout>
    <form method="POST" action="{{ route('module.name.show', ['nommodule' => $nommodule]) }}/exams/create">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Nom de l\'examen')" />
            <x-text-input id="exam-name" class="block mt-1 w-full" type="text" name="exam-name" :value="old('name')" required autofocus autocomplete="exam-name" />
            <x-input-error :messages="$errors->get('exam-name')" class="mt-2" />
        </div>
            <!-- Coef -->
        <div>
            <x-input-label for="name" :value="__('Coefficient')" />
            <x-text-input id="coefficient" class="block mt-1 w-full" type="number" name="coefficient" :value="old('1')" required autofocus autocomplete="coefficient" />
            <x-input-error :messages="$errors->get('coefficient')" class="mt-2" />
        </div>
        <div class="flex items-center justify-end mt-4">
            <x-primary-button class="ml-4">
                {{ __('Créer') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
