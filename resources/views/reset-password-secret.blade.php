<x-guest-layout>
    <form method="POST" action="{{ route('password.reset.final') }}">
        @csrf

        <div class="mb-4 text-sm text-gray-600">
            {{ __('Répondez à la question secrète pour réinitialiser votre mot de passe.') }}
        </div>

        <!-- Security Question -->
        <div class="mt-4">
            <x-input-label :value="$securityQuestion" />
            <x-text-input id="security_answer" class="block mt-1 w-full" type="text" name="security_answer" :value="old('security_answer')" required />
            <x-input-error :messages="$errors->get('security_answer')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-secondary-button>
                <a href="{{ route('login') }}">
                    {{ __('Annuler') }}
                </a>
            </x-secondary-button>

            <x-primary-button class="ml-4">
                {{ __('Réinitialiser le mot de passe') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
