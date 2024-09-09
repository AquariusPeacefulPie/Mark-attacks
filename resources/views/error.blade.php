<!-- error.blade.php -->

<x-guest-layout>
    <div class="mt-4 text-center">
        <h2 class="text-2xl font-bold text-red-600">Erreur d'accès</h2>
        <p class="mt-2 text-sm text-gray-600">Vous n'êtes pas autorisé à accéder à cette page.</p>
        <a href="{{ url('/dashboard') }}" class="mt-4 text-indigo-600 hover:text-indigo-500">Retour à la page d'accueil</a>
    </div>
</x-guest-layout>
