<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Examen n°' . $exam_id . ' du module ' . $module_name) }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <header>
                            @if(auth()->user()->role != 'student')
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ __('Liste des participants') }}
                            </h2>

                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                {{ __("Vous pouvez ajouter, modifier ou supprimer une note au participants ci-dessous") }}
                            </p>
                            @else
                                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                    {{ __('Ma note') }}
                                </h2>

                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    {{ __("Vous pouvez voir votre note ci-dessous pour cet examen") }}
                                </p>
                            @endif
                        </header>
                            @if (count($examinees))
                                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                                        <div class="p-6 text-gray-900 dark:text-gray-100">
                                            <table class="min-w-full border border-gray-300">
                                                <thead>
                                                <tr class="bg-gray-50 dark:bg-gray-700">
                                                    <th class="py-2 px-4 border-b">Prénom</th>
                                                    <th class="py-2 px-4 border-b">Nom</th>
                                                    <th class="py-2 px-4 border-b">Note</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($examinees as $examinee)
                                                        @if($examinee->id == auth()->user()->id || auth()->user()->role == 'teacher')
                                                        <tr class="border-b">
                                                            <td class="py-2 px-4 border-b text-center">{{ $examinee->firstname }}</td>
                                                            <td class="py-2 px-4 border-b text-center">{{ $examinee->lastname }}</td>
                                                            <td class="py-2 px-4 border-b text-center">
                                                                @if(auth()->user()->role != 'student')
                                                                <label>
                                                                    <input
                                                                        type="number"
                                                                        value="{{ $examinee->mark }}"
                                                                        data-module-name="{{ $module_name }}"
                                                                        data-exam-id="{{ $exam_id }}"
                                                                        data-examinee-id="{{ $examinee->id }}"
                                                                        class="update-mark-input text-center dark:text-black"
                                                                        style="max-width: 100px"
                                                                    >
                                                                </label>
                                                                @else
                                                                {{$examinee->mark}}
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        @endif
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                                        <div class="p-6 text-gray-900 dark:text-gray-100">
                                            <p>Pas de participant à cet examen.</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const updateMarkInputs = document.querySelectorAll('.update-mark-input');

        updateMarkInputs.forEach((input) => {
            input.addEventListener('change', () => {
                const moduleName = input.dataset.moduleName;
                const examId =input.dataset.examId;
                const examineeId = input.dataset.examineeId;
                const newMark = input.value;

                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                // Send XHR Request to modify mark in DB
                const xhr = new XMLHttpRequest();
                xhr.open('POST', '/module/' + moduleName + '/exam/' + examId + '/' + examineeId);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
                xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);

                xhr.onreadystatechange = () => {
                    if (xhr.readyState === 4) {
                        if (xhr.status === 200) {
                            console.log('mark updated');
                        }
                        else {
                            console.error('error while updating mark');
                        }
                    }
                }
                xhr.send(`mark=${newMark}`);
            });
        });
    })
</script>
