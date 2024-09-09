<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Module '.$module->name) }}
        </h2>
    </x-slot>
    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg flex justify-between"> <!-- Utilisation de la classe "flex justify-between" -->
                <div class="max-w-xl">
                    <div class="justify-content-center">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ __('Examens du module') }}
                        </h2>
                        <a href="{{ route('module.name.exams.index', ['nommodule' => $module->name]) }}">
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                {{ __("Consulter les examens du module") }}
                            </p>
                        </a>
                    </div>
                </div>
                <div>
                    @php
                    $res = false;
                    if(auth()->user()->role == "student"){
                        $student = \App\Models\Student::select('students.id')->where('user_id',auth()->user()->id)->first();
                        $count = \App\Models\StudentModule::where('student_id',$student->id)->where('module_id', $module->id)->count();
                        $res = $count === 0;
                    }elseif (auth()->user()->role == "teacher"){
                        $teacher = \App\Models\Teacher::select('teachers.id')->where('user_id',auth()->user()->id)->first();
                        $count = \App\Models\TeacherModule::where('teacher_id',$teacher->id)->where('module_id', $module->id)->count();
                        $res= $count === 0;
                    }
                    @endphp
                    @if ($res)
                    <form method="POST" action="{{ route('module.name.assign', ['nommodule' => $module->name,'type'=> 'ask']) }}">
                        @csrf
                        <input type="hidden" name="module_id" value="{{ $module->id }}">
                        <button id="askAccessButton" type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            {{ __("Demander accès au module") }}
                        </button>
                    </form>
                    @else
                        <button disabled class="bg-gray-300 text-gray-500 font-bold py-2 px-4 rounded cursor-not-allowed">
                            {{ __("Accès déjà demandé") }}
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('modules.partials.update-coefficient-form')
                </div>
            </div>
        </div>
    </div>

    <!-- Teachers and Students Columns -->
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Display status information -->
            <div class="mt-4">
                @if(session('status'))
                <div class="bg-green-500 p-4 rounded-lg mb-6 text-white text-center">
                    {{ session('status') }}
                </div>
                @endif
            </div>
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <div class="flex">
                        <!-- Teachers Column -->
                        <div class="w-1/2 pr-4">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                    {{ __('Enseignants') }}
                                </h2>
                                @if(auth()->user()->role != 'student')
                                    <a id="addTeacherToModuleButton" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-2 rounded" href="{{ route('module.name.add-teachers', ['nommodule' => $module->name]) }}"> Ajouter enseignant </a>
                                @endif
                            </div>
                            <!-- List of Teachers -->
                            <table class="min-w-full border border-gray-300">
                                <thead>
                                    <tr class="bg-gray-50 dark:bg-gray-700">
                                        <th class="py-2 text-center px-4 border-b dark:text-gray-100">Nom</th>
                                        <th class="py-2 text-center px-4 border-b dark:text-gray-100">Prénom</th>
                                    </tr>
                                </thead>
                                <tbody> @foreach($teachers as $teacher) <tr class="border-b">
                                        <td class="py-2 text-center px-4 border-b dark:text-gray-100">{{ $teacher->lastname }}</td>
                                        <td class="py-2 text-center px-4 border-b dark:text-gray-100">{{ $teacher->firstname}}</td>
                                    </tr> @endforeach </tbody>
                            </table>
                        </div>
                        <!-- Students Column -->
                        <div class="w-1/2 pl-4">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                    {{ __('Etudiants') }}
                                </h2>
                                @if(auth()->user()->role != 'student')
                                    <a id="addStudentToModuleButton" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-2 rounded" href="{{ route('module.name.add-students', ['nommodule' => $module->name]) }}"> Ajouter étudiant </a>
                                @endif
                            </div>
                            <!-- List of Students -->
                            <table class="min-w-full border border-gray-300">
                                <thead>
                                    <tr class="bg-gray-50 dark:bg-gray-700">
                                        <th class="py-2 text-center px-4 border-b dark:text-gray-100">Nom</th>
                                        <th class="py-2 text-center px-4 border-b dark:text-gray-100">Prénom</th>
                                    </tr>
                                </thead>
                                <tbody> @foreach($students as $student) <tr class="border-b" id="student{{$student->id}}">
                                        <td class="py-2 text-center px-4 border-b dark:text-gray-100">{{ $student->lastname  }}</td>
                                        <td class="py-2 text-center px-4 border-b dark:text-gray-100">{{ $student->firstname }}</td>
                                    </tr> @endforeach </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
