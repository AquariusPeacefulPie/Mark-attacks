<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Informations du profil') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Modifiez les informations ci-dessous.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>


    <form method="post" action="{{ route('liste-utilisateurs.update', ['type' => $type, 'id' => $user->id]) }}"
          class="mt-6 space-y-6">
        @csrf
        @method('put')
        @php
        $firstname = $user->role ? $user->firstname : $user->user->firstname;
        $lastname = $user->role ? $user->lastname : $user->user->lastname;
        $birthdate = $user->role ? $user->birthdate : $user->user->birthdate;
        $email = $user->role ? $user->email : $user->user->email;
        @endphp
        <div>
            <x-input-label for="firstname" :value="__('Prénom')"/>
            <x-text-input id="firstname" name="firstname" type="text" class="mt-1 block w-full"
                          :value="old('firstname', $firstname)" required autofocus autocomplete="firstname"/>
            <x-input-error class="mt-2" :messages="$errors->get('firstname')"/>
        </div>

        <div>
            <x-input-label for="lastname" :value="__('Nom')"/>
            <x-text-input id="lastname" name="lastname" type="text" class="mt-1 block w-full"
                          :value="old('lastname', $lastname)" required autofocus autocomplete="lastname"/>
            <x-input-error class="mt-2" :messages="$errors->get('lastname')"/>
        </div>

        <div>
            <x-input-label for="birthdate" :value="__('Date de naissance')"/>
            <x-text-input id="birthdate" name="birthdate" type="date" class="mt-1 block w-full"
                          :value="old('birthdate', $birthdate)" required autofocus autocomplete="birthdate"/>
            <x-input-error class="mt-2" :messages="$errors->get('birthdate')"/>
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')"/>
            <x-text-input id="email" name="email" type="text" class="mt-1 block w-full"
                          :value="old('email', $email)" required autofocus autocomplete="email"/>
            <x-input-error class="mt-2" :messages="$errors->get('email')"/>
        </div>


        <div class="flex items-center gap-4">
            <x-primary-button id="confirmUpdateButton">{{ __('Sauvegarder') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('Sauvegardé.') }}</p>
            @endif
        </div>
    </form>
</section>
