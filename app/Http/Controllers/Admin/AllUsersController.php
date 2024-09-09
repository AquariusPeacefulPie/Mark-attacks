<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Admin;
use App\Models\StudentModule;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;
class AllUsersController extends Controller
{
/*
    public function listeUtilisateurs()
    {
        $students = Student::all();
        return view('liste-utilisateurs', ['students' => $students]);
    }
*/

    public function listeUtilisateurs($type)
    {
        if ($type == 'admins') {
            $users = Admin::paginate(5);
        } elseif ($type == 'teachers') {
            $users = Teacher::paginate(5);
        } elseif ($type == 'students'){
            $users = Student::paginate(5);
        }elseif ($type == 'users'){
            $users = User::paginate(5);
        }else{
            $inactiveTeachers = Teacher::where('active', false)->with('user')->get()->pluck('user');
            $inactiveStudents = Student::where('active', false)->with('user')->get()->pluck('user');
            $users = $inactiveTeachers->merge($inactiveStudents)->unique();
        }

        return view('liste-utilisateurs', [
            'users' => $users,
            'selectedCategory' => $type,
        ]);
    }



    public function activateAccount($type, $id)
    {
        switch ($type) {
            case 'admins':
                $user = Admin::findOrFail($id);
                break;
            case 'teachers':
                $user = Teacher::findOrFail($id);
                break;

            case 'students':
                $user = Student::findOrFail($id);
                break;
            case 'inactive users':
                $user = User::findOrFail($id);
                break;
            default:
                $user = User::findOrFail($id);
                break;

        }
        if($type == 'users' || $type == 'inactive users'){

            $role = DB::table('users')->where('id', $id)->value('role');
            $table = $role.'s';
            $usr = DB::table($table)->where('user_id', $id)->update(['active' => 1]);
        }else if ($type == 'students' || $type == 'teachers'){
            $user->active = true;
            $user->save();
        }else{
            $user->save();
        }


        return redirect()->route('liste-utilisateurs', ['type' => $type])->with('success', 'Le compte a été activé avec succès.');
    }


    public function getInactiveUsers()
    {
        $inactiveTeachers = Teacher::where('active', false)->with('user')->get()->pluck('user');
        $inactiveStudents = Student::where('active', false)->with('user')->get()->pluck('user');
        $inactiveUsers = $inactiveTeachers->merge($inactiveStudents)->unique();

        //return View::make('inactive_users')->with('inactiveUsers', $inactiveUsers);
        return view('dashboard')->with('inactiveUsers', $inactiveUsers);

    }

    public function showDashboard()
    {
        $user = Auth::user();

        if ($user->role == "admin") {
            return $this->getInactiveUsers();
        } elseif ($user->role == "teacher") {
            return view('dashboard');
        } elseif ($user->role == "student") {
            return $this->showStudentModules();
        }

    }

    public function showStudentModules()
    {
        $user = Auth::user();
        $studentId = Student::where('user_id', $user->id)->value('id');
        $studentModules = StudentModule::where('student_id', $studentId)
            ->with('module') // Charger les modules associés
            ->get();
        return view('dashboard', ['studentModules' => $studentModules]);
        //return view('dashboard')->with('inactiveUsers', $inactiveUsers);
    }







    public function destroy($type, $id)
    {
        switch ($type) {
            case 'admins':
                $utilisateur = Admin::findOrFail($id);
                break;
            case 'teachers':
                $utilisateur = Teacher::findOrFail($id);
                break;
            case 'students':
                $utilisateur = Student::findOrFail($id);
                break;
            default:
                $utilisateur = User::findOrFail($id);
                break;
        }

        if($type == 'users' || $type == 'inactive users'){
            $user = User::findOrFail($id);
            $user->delete();
        }else{
            $user = User::findOrFail($utilisateur->user_id);
            // Supprimer l'utilisateur
            $user->delete();
        }

        return redirect()->route('liste-utilisateurs', ['type' => $type])->with('successSup', 'L\'utilisateur a été supprimé avec succès.');
    }

    public function editAdmin($type, $id)
    {
        switch ($type) {
            case 'admins':
                $user = Admin::findOrFail($id);
                break;
            case 'teachers':
                $user = Teacher::findOrFail($id);
                break;
            case 'students':
                $user = Student::findOrFail($id);
                break;
            default:
                $user =  User::findOrFail($id);
                break;
        }

        return view('profile.edit-admin', compact('user', 'type'));
    }




    public function update(Request $request, $type, $id)
    {
        $request->validate([
            'firstname' => 'string|max:255',
            'lastname' => 'string|max:255',
            'birthdate' => 'date',
        ]);

        switch ($type) {
            case 'admins':
                $user = Admin::findOrFail($id);
                break;
            case 'teachers':
                $user = Teacher::findOrFail($id);
                break;
            case 'students':
                $user = Student::findOrFail($id);
                break;
            default:
                $user = User::findOrFail($id);
                break;
        }

        if($type == 'users'){
              $usr = DB::table('users')
                ->where('id', $id)
                ->update([
                    'firstname' => $request->input('firstname'),
                    'lastname' => $request->input('lastname'),
                    'birthdate' => $request->input('birthdate'),
                    'email' => $request->input('email'),
                ]);
        }else{
            $user2 = User::findOrFail($user->user_id);
            $user2->fill($request->all());
            $user2->save();
        }
        return redirect()->route('liste-utilisateurs', ['type' => $type])->with('success', 'Les informations de l\'utilisateur ont été mises à jour avec succès.');
    }
}
