<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Gestion des demandes d\'accès à mes modules') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <script>
            function confirmReject(url) {
                if (confirm('Êtes-vous sûr de vouloir refuser l\'accès au module pour cet utilisateur?')) {
                    window.location.href = url;
                }
            }
            function confirmAccept(url) {
                if (confirm('Êtes-vous sûr de vouloir accepter l\'accès au module pour cet utilisateur?')) {
                    window.location.href = url;
                }
            }
        </script>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">


                    <table class="min-w-full border border-gray-300">
                        <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700">
                            <th class="py-2 px-4 border-b">Nom</th>
                            <th class="py-2 px-4 border-b">Prénom</th>
                            <th class="py-2 px-4 border-b">Role</th>
                            <th class="py-2 px-4 border-b">Module demandé</th>
                            <th class="py-2 px-4 border-b">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($modules as $module)
                            @php
                                $users = \Illuminate\Support\Facades\DB::table('users')
                                    ->select('users.id', 'users.firstname', 'users.lastname', 'users.role')
                                    ->leftJoin('teachers', 'teachers.user_id', '=', 'users.id')
                                    ->leftJoin('teacher_modules', 'teachers.id', '=', 'teacher_modules.teacher_id')
                                    ->leftJoin('students', 'students.user_id', '=', 'users.id')
                                    ->leftJoin('student_modules', 'students.id', '=', 'student_modules.student_id')
                                    ->where(function ($query) use ($module){
                                        $query->orWhere(function ($query) use ($module){
                                            $query->where('teacher_modules.module_id', $module->id)
                                                  ->where('teacher_modules.active', 0);
                                        })->orWhere(function ($query) use ($module){
                                            $query->where('student_modules.module_id', $module->id)
                                                  ->where('student_modules.active', 0);
                                        });
                                    })
                                    ->get();
                            @endphp

                            @foreach($users as $user)
                                @php


                                    $studentModule = \App\Models\StudentModule::select('student_modules.id')->join('students', 'student_modules.student_id', '=', 'students.id')
                                         ->where('students.user_id', $user->id)
                                         ->where('student_modules.module_id', $module->id)
                                         ->first();

                                    $teacherModule = \App\Models\TeacherModule::select('teacher_modules.id')->join('teachers', 'teacher_modules.teacher_id', '=', 'teachers.id')
                                         ->where('teachers.user_id', $user->id)
                                         ->where('teacher_modules.module_id', $module->id)
                                         ->first();

                                     $userModuleId = -1;
                                     if ($user->role == "student"){
                                         $userModuleId = $studentModule->id;
                                     }
                                     else{
                                         $userModuleId = $teacherModule->id;
                                     }


                                @endphp
                                <tr class="border-b">
                                    <td class="py-2 px-4 border-b">{{ $user->firstname}} </td>
                                    <td class="py-2 px-4 border-b">{{ $user->lastname}} </td>
                                    <td class="py-2 px-4 border-b">{{ $user->role}} </td>
                                    <td class="py-2 px-4 border-b">{{ $module->name}} </td>
                                    <td class="py-2 px-4 border-b">
                                        <a id="accept{{$user->firstname}}" href="#" onclick="confirmAccept('{{ route('demandes.liste-utilisateurs-demandes-module.activate', ['userModuleId' => $userModuleId, 'role' => $user->role]) }}')" class="text-green-500 hover:underline mr-2">
                                            <i class="fas fa-check-circle text"></i> Activer
                                        </a>
                                        <a id="reject{{$user->firstname}}" href="#" class="text-red-500 hover:underline" onclick="confirmReject('{{ route('demandes.liste-utilisateurs-demandes-module.reject', ['userModuleId' => $userModuleId, 'role' => $user->role]) }}')">
                                            <i class="fas fa-trash-alt"></i> Supprimer
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                      @endforeach

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
