<x-guest-layout>
    <form method="POST" action="{{route('module.create')}}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Nom du module')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="nom" :value="old('nom')" required autofocus autocomplete="nom" />
            <x-input-error :messages="$errors->get('nom')" class="mt-2" />
        </div>
        <!-- Coef -->
        <div>
            <x-input-label for="name" :value="__('Coefficient')" />
            <x-text-input id="coefficient" class="block mt-1 w-full" type="number" name="coefficient" :value="old('1')" required autofocus autocomplete="coefficient" />
            <x-input-error :messages="$errors->get('coefficient')" class="mt-2" />
        </div>
        <x-input-label for="name" :value="__('Professeur Responsable')" />
        <div class="form-group row">
            <div class="col-sm-8">
                <select class="form-control" id="user_id" name="user_id" required focus for="name" :value="__('Prefesseur Responsable')">
                    <option value="" disabled selected>Choisir un professeur</option>
                    @foreach($teachers as $teacher)
                        <option id="dropdownOption{{$teacher->id}}" value="{{$teacher->id}}">{{ $teacher->lastname }} {{ $teacher->firstname }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <script>
            document.getElementById("user_id").addEventListener("change", function() {
                var selectedTeacherId = this.value;
                document.getElementById("user_id").value = selectedTeacherId;
            });
        </script>
        <div class="flex items-center justify-end mt-4">
            <x-primary-button class="ml-4" id="createModuleButton">
                {{ __('Créer') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
