<?php

namespace App\Http\Controllers;

use App\Http\Requests\ModuleUpdateRequest;
use App\Models\Module;
use App\Models\Student;
use App\Models\StudentModule;
use App\Models\Teacher;
use App\Models\TeacherModule;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ModuleController extends Controller
{
    public function index()
    {
        $modules = Module::all(); // Récupérer tous les modules depuis la base de données
        $users = User::all();

        foreach ($modules as $module) {
            //add the average mark to the module for the current user
            $module->average_student = $module->calculateAverageMarkByStudent(auth()->user()->id);

            // if empty, set the average mark to '---', else format it to 2 decimals
            // dd($module);    
            $module->average_student = $module->average_student ? number_format($module->average_student, 2) : '---';

            //add the average mark to the module for all students
            $module->average_mark = $module->calculateAverageMark();

            // if empty, set the average mark to '---', else format it to 2 decimals
            $module->average_mark = $module->average_mark ? number_format($module->average_mark, 2) : '---';

        }

        // Charger la vue et passer les modules
        return view('modules.index', compact('modules', 'users'));
    }


    public function show(string $nommodule)
    {
        $module = Module::where('name', $nommodule)->first();

        if (!$module)
            return redirect()->route('module.index')->with('error', 'Module non trouvé'); // Redirection avec message d'erreur

        return view('modules.show', compact('module'));
    }

    public function choisirModule($module_id)
    {
        $module = Module::find($module_id);
        $nomModule = $module->name;
        return redirect()->route('module.name.show', ['nommodule' => $nomModule]);
    }


    public function showStudents($nommodule)
    {
        $module = Module::where('name', $nommodule)->first();

        //select all student that are not already in the module (use Student and StudentModule models)
        $students = Student::select('users.firstname', 'users.lastname', 'students.id')
            ->join('users', 'users.id', '=', 'students.user_id')
            ->whereNotIn('students.id', function($query) use ($module) {
                $query->select('student_modules.student_id')->from('student_modules')->where('student_modules.module_id', $module->id);
            })
            ->get();

        return view('modules.add-students', [
            'module' => $module,
            'students' => $students,
            'nommodule' => $module->name,
        ]);
    }

    public function showTeachers($nommodule)
    {
        $module = Module::where('name', $nommodule)->first();
        //select all teacher that are not already in the module
        $teachers = Teacher::select('users.firstname', 'users.lastname', 'teachers.id')
            ->join('users', 'users.id', '=', 'teachers.user_id')
            ->whereNotIn('teachers.id', function($query) use ($module) {
                $query->select('teacher_modules.teacher_id')->from('teacher_modules')->where('teacher_modules.module_id', $module->id);
            })
            ->get();
        //in the url, nommodule is a parameter, we need to pass it to the view
        return view('modules.add-teachers', [
            'module' => $module,
            'teachers' => $teachers,
            'nommodule' => $module->name,
        ]);
    }

    public function assignStudents(Request $request, $nommodule)
    {
        // Retrieve the selected student IDs from the form
        $selectedStudentIds = $request->input('selected_students', []);

        // Find the module by name
        $module = Module::where('name', $nommodule)->first();

        // Assign the students to the student_modules pivot table using DB::
        DB::table('student_modules')->insert(
            array_map(function ($studentId) use ($module) {
                return [
                    'student_id' => $studentId,
                    'module_id' => $module->id,
                    'active'=> 1
                ];
            }, $selectedStudentIds)
        );

        $nomModule = $module->name;
        return redirect()->route('module.name.show', ['nommodule' => $nomModule])->with('status', 'Le(s) étudiant(s) ont été ajouté(s) au module');
    }

    public function assignTeachers(Request $request, $nommodule)
    {
        // Retrieve the selected teacher IDs from the form
        $selectedTeacherIds = $request->input('selected_teachers', []);

        // Find the module by name
        $module = Module::where('name', $nommodule)->first();

        // Assign the teachers to the teacher_modules pivot table using DB::
        DB::table('teacher_modules')->insert(
            array_map(function ($teacherId) use ($module) {
                return [
                    'teacher_id' => $teacherId,
                    'module_id' => $module->id,
                    'active'=> 1
                ];
            }, $selectedTeacherIds)
        );

        $nomModule = $module->name;
        return redirect()->route('module.name.show', ['nommodule' => $nomModule])->with('status', 'Le(s) enseignant(s) ont été ajouté(s) au module');
    }

    public function assign(Request $request, $nommodule)
    {
        if ($request->input('type') == 'student') {
            return $this->assignStudents($request, $nommodule);
        } else if ($request->input('type') == 'teacher') {
            return $this->assignTeachers($request, $nommodule);
        } else if ($request->input('type') == 'ask') {
            return $this->askAssign($request,$nommodule);
        }
    }

    public function askAssign(Request $request, $nommodule)
    {
        if (auth()->user()->role == 'student') {
            return $this->askAssignStudents($request,$nommodule,auth()->user()->id);
        } else if (auth()->user()->role == 'teacher') {
            return $this->askAssignTeachers($request,$nommodule,auth()->user()->id);
        }
    }

    public function askAssignStudents(Request $request, $nommodule,$user_id)
    {

        // Find the module by name
        $module = Module::where('name', $nommodule)->first();
        $student = Student::where('user_id',$user_id)->first();
        // Assign the students to the student_modules pivot table using DB::
        DB::table('student_modules')->insert(
            ['student_id'=>$student->id,
                'module_id'=>$module->id,
                'active'=>0]
        );

        $nomModule = $module->name;
        return redirect()->route('module.name.show', ['nommodule' => $nomModule])->with('status', 'Demande d\'accès au module effectué.');
    }

    public function askAssignTeachers(Request $request, $nommodule,$user_id)
    {
        // Find the module by name
        $module = Module::where('name', $nommodule)->first();
        $teacher = Teacher::where('user_id',$user_id)->first();
        // Assign the teachers to the teacher_modules pivot table using DB::
        DB::table('teacher_modules')->insert(
            ['teacher_id'=>$teacher->id,
                'module_id'=>$module->id,
                'active'=>0]
        );

        $nomModule = $module->name;
        return redirect()->route('module.name.show', ['nommodule' => $nomModule])->with('status', 'Demande d\'accès au module effectué.');
    }


    public function StudentAndTeacherInModule($nommodule)
    {
        $module = Module::where('name', $nommodule)->first();

        $students = DB::table('student_modules')
            ->join('students','students.id', '=', 'student_modules.student_id')
            ->join('users' , 'users.id', '=', 'students.user_id')
            ->select('users.*')
            ->where('student_modules.module_id', $module->id)
            ->where('student_modules.active', 1)
            ->get();

        $teachers = DB::table('teacher_modules')
            ->join('teachers','teachers.id', '=', 'teacher_modules.teacher_id')
            ->join('users' , 'users.id', '=', 'teachers.user_id')
            ->select('users.*')
            ->where('teacher_modules.module_id', $module->id)
            ->where('teacher_modules.active', 1)
            ->get();

        //add the average mark to the module for all students
        $module->average_mark = $module->calculateAverageMark();

        // if empty, set the average mark to '---', else format it to 2 decimals
        $module->average_mark = $module->average_mark ? number_format($module->average_mark, 2) : '---';

        //test if the current user is a student (user.type == student)
        if (auth()->user()->role == 'student') {
            $averageMark = $module->calculateAverageMarkByStudent(auth()->user()->id);

            // if empty, set the average mark to '---', else format it to 2 decimals
            $averageMark = $averageMark  ? number_format($averageMark, 2) : '---';

            return view('modules.show', compact('module', 'teachers', 'students', 'averageMark'));
        }

        return view('modules.show', compact('module', 'teachers', 'students'));
    }

    public function update(Request $request): RedirectResponse
    {
        $moduleID = $request->input('module_id');

        // Recherchez le module en fonction de son nom
        $module = Module::where('id', $moduleID)->first();
        // Vérifiez si le module existe
        if (!$module) {
            return redirect()->route('module.index')->with('error', 'Module non trouvé');
        }

        // Mettez à jour le coefficient du module
        $module->update([
            'name' => $request->input('name'),
            'coefficient' => $request->input('coefficient'),
            'user_id' => $module->user_id
        ]);


        return Redirect::route('module.index')->with('status', 'module-updated');
    }

    public function create(){
        $teachers = User::where('role', 'teacher')->get();
        return view('modules.create',compact( 'teachers'));
    }

    public function store(Request $request) {
        $newModule = new Module([
            'name' => $request->input('nom'),
            'coefficient' => $request->input('coefficient'),
            'user_id' => $request->input('user_id')
        ]);
        $newModule->save();
        $newTeacherModule = new TeacherModule([
            'teacher_id' => Teacher::where('user_id', $newModule->user_id)->first()->id,
            'module_id' => $newModule->id,
            'active' => true
        ]);
        $newTeacherModule->save();
        return Redirect::route('module.index')->with('status', 'module-updated')->with('success', 'Nouveau module ajouté !');
    }
    public function delete($id) {
        $module = Module::find($id);
        $module->delete();
        return Redirect::route('module.index')->with('status', 'module-deleted')->with('success', 'Le module ' . $module->name . ' a été supprimé');
    }

    public function showDemands(){
        $modules = Module::where('user_id',Auth::user()->id)->get();
        return view('demandes.liste-utilisateurs-demandes-module', compact('modules'));
    }

    public function acceptDemand($userModuleId,$role){
            $table = $role.'_modules';
            $usr = DB::table($table)->where('id', $userModuleId)->update(['active' => 1]);

        return Redirect::route('demandes.liste-utilisateurs-demandes-module')->with('status', 'demand-accepted')->with('success', 'L\'utilisateur a rejoint le module');

    }

    public function rejectDemand($userModuleId,$role){
        $table = $role.'_modules';
        $usr = DB::table($table)->where('id', $userModuleId)->delete();

        return Redirect::route('demandes.liste-utilisateurs-demandes-module')->with('status', 'demand-rejected')->with('success', 'L\'utilisateur n\'a pas rejoint le module');

    }
}
