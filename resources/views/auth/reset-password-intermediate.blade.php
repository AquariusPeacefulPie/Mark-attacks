<!-- reset-password-intermediate.blade.php -->
<x-guest-layout>
    <form method="POST" action="{{ route('password.check-secret') }}">
        @csrf
        @method('POST')
    <!-- Email Address -->
    <div class="mt-4">
        <x-input-label for="email" :value="__('Email')" />
        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>

    <!-- Security Question -->
    <div class="mt-4">
        <x-input-label for="security_answer" :value="__('Question secrète: Qui a été le pire professeur de cette année ?')" />
        <x-text-input id="security_answer" class="block mt-1 w-full" type="text" name="security_answer" required />
        <x-input-error :messages="$errors->get('security_answer')" class="mt-2" />
    </div>

    <div class="flex items-center justify-end mt-4">
        <x-primary-button class="ml-4" type="submit" id="continueResetButton">
            {{ __('Continuer') }}
        </x-primary-button>
    </div>
    </form>
</x-guest-layout>
