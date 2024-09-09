<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Liste des examens pour le module : ' . $module_name) }}
        </h2>
    </x-slot>
    @if(session('success'))
        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                        <div class="alert alert-success text-sm text-gray-600 dark:text-gray-400" role="alert">
                            {{  session('success')  }}
                        </div>
                </div>
            </div>
        </div>
    @endif
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ __('Examens') }}
                            </h2>
                            @if(auth()->user()->role != 'student')
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                {{ __("Vous pouvez créer, modifier ou consulter un examen ci-dessous") }}
                            </p>
                            @else
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    {{ __("Vous pouvez consulter un examen ci-dessous") }}
                                </p>
                            @endif
                        </header>

                        @if(auth()->user()->role != 'student')
                            <div class="flex justify-content-center mt-4">
                                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                                   href="{{ route('module.name.exams.create', ['nommodule' => $module_name]) }}">Créer un nouvel examen
                                </a>
                            </div>
                        @endif


                        @if (count($exams))
                            <div id="table-exams-wrapper" class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                                    <div class="p-6 text-gray-900 dark:text-gray-100">
                                        <table class="min-w-full border border-gray-300">
                                            <thead>
                                            <tr class="bg-gray-50 dark:bg-gray-700">
                                                <th class="py-2 px-4 border-b">Nom de l'examen</th>
                                                <th class="py-2 px-4 border-b">Coefficient</th>
                                                <th class="py-2 px-4 border-b">Note moyenne</th>
                                                @if(auth()->user()->role != 'student')
                                                    <th class="py-2 px-4 border-b">Actions</th>
                                                @endif
                                            </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($exams as $exam)
                                                    <tr class="border-b clickable-row" data-href="{{ route('module.name.exam.show', ['nommodule' => $module_name, 'exam_id' => $exam->id]) }}" data-exam-id="{{ $exam->id }}" style="cursor: pointer;">
                                                        <td class="py-2 px-4 border-b text-center">{{ $exam->name }}</td>
                                                        <td class="py-2 px-4 border-b text-right">{{ $exam->coefficient }}</td>
                                                        <td class="py-2 px-4 border-b text-right">{{ '---' }}</td>
                                                        @if(auth()->user()->role != 'student')
                                                            <td>
                                                                <div class="flex justify-center">
                                                                    <img src="{{ asset('exams/trash.svg') }}" class="delete-exam-button" data-module-name="{{ $module_name }}" data-exam-id="{{ $exam->id }}" alt="trash_logo">
                                                                </div>
                                                            </td>
                                                        @endif
                                                    </tr>
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
                                        <p>Pas d'examen disponible.</p>
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
    document.addEventListener("DOMContentLoaded", function() {
        const rows = document.querySelectorAll(".clickable-row");
        rows.forEach((row) => {
            row.addEventListener("click", () => {
                window.location.href = row.getAttribute("data-href");
            });
        });

        const deleteButtons = document.querySelectorAll(".delete-exam-button");
        deleteButtons.forEach((button) => {
           button.addEventListener("click", (event) => {
               event.stopPropagation();
               const moduleName = button.getAttribute("data-module-name");
               const examId = button.getAttribute("data-exam-id");

               // CSRF protection
               const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

               // Send xhr request to delete corresponding exam
               const xhr = new XMLHttpRequest();
               xhr.open("POST", "/module/" + moduleName + "/exam/" + examId + "/delete");
               xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
               xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);

               xhr.onreadystatechange = () => {
                   if (xhr.readyState === 4) {
                       if (xhr.status === 200) {
                           // Suppress row in the DOM
                           document.querySelector(`[data-exam-id="${examId}"]`).remove();
                           // Test if table is empty
                           const rows = document.querySelectorAll('tbody tr');
                           if (rows.length === 0) {
                               document.getElementById('table-exams-wrapper').remove();

                               const containerDiv = document.createElement('div');
                               containerDiv.className = 'max-w-7xl mx-auto sm:px-6 lg:px-8';

                               const contentDiv = document.createElement('div');
                               contentDiv.className = 'bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg';

                               const innerDiv = document.createElement('div');
                               innerDiv.className = 'p-6 text-gray-900 dark:text-gray-100';

                               const paragraph = document.createElement('p');
                               paragraph.textContent = "Pas d'examen disponible.";

                               innerDiv.appendChild(paragraph);
                               contentDiv.appendChild(innerDiv);
                               containerDiv.appendChild(contentDiv);

                               document.getElementsByTagName('section')[0].appendChild(containerDiv);
                           }
                           console.log("exam successfully deleted");
                       }
                       else {
                           console.error('error deleting exam');
                       }
                   }
               }
               xhr.send();
           });
        });
    });
</script>
