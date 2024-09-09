<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Menu principal') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("Bienvenue") }}
                </div>
            </div>
        </div>
    </div>

    @if(Auth::user()->role == 'admin')
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    {{-- Inclure la vue des utilisateurs inactifs --}}
                    @include('inactive_users')
                </div>
            </div>
        </div>
    @endif

    @if(Auth::user()->role == 'student')
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    {{-- Inclure la vue des utilisateurs inactifs --}}
                    @include('student_modules')
            </div>
        </div>
    @endif





</x-app-layout>
