<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="firstname" :value="__('Prénom')" />
            <x-text-input id="firstname" class="block mt-1 w-full" type="text" name="firstname" :value="old('firstname')" required autofocus autocomplete="firstname" />
            <x-input-error :messages="$errors->get('firstname')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="lastname" :value="__('Nom')" />
            <x-text-input id="lastname" class="block mt-1 w-full" type="text" name="lastname" :value="old('lastname')" required autofocus autocomplete="lastname" />
            <x-input-error :messages="$errors->get('lastname')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="birthdate" :value="__('Date de naissance')" />
            <input id="birthdate" type="date" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50" name="birthdate" :value="{{ old('birthdate') }}" required autofocus autocomplete="birthdate">

            <x-input-error :messages="$errors->get('birthdate')" class="mt-2" />
        </div>


        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Security Question -->
        <div class="mt-4">
            <x-input-label :value="__('Qui a été le pire professeur de cette année ?')" />
            <x-text-input id="security_answer" class="block mt-1 w-full" type="text" name="security_answer" :value="old(' ')" required />
            <x-input-error :messages="$errors->get('security_answer')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Mot de passe')" />

            <x-text-input id="password" class="block mt-1 w-full"
                          type="password"
                          name="password"
                          required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirmation du mot de passe')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                          type="password"
                          name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-4 flex items-center">
            <input
                id="is_teacher"
                type="checkbox"
                class="form-checkbox h-5 w-5 text-indigo-600 transition duration-150 ease-in-out"
                name="is_teacher"
                :checked="old('is_teacher')"
            />
            <label for="is_teacher" class="ml-2 block text-sm font-medium text-gray-700 dark:text-gray-200">Je suis un professeur</label>
        </div>





        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
                {{ __('Déjà enregistré?') }}
            </a>

            <x-primary-button id="validateRegister" class="ml-4">
                {{ __('S\'inscrire') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
