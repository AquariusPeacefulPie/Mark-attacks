<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CreateAdminController extends Controller
{

    public function create($category)
    {
        $selectedCategory = $category;
        return view('create-admin', compact('selectedCategory'));
    }


    public function store(Request $request , $category)
    {
        $request->validate([
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'birthdate' => ['required', 'date'],
            'security_answer' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $cat = substr($category, 0, strlen($category) - 1);

        // Choisir le modèle en fonction de l'utilisateur
        $attributes = [
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'birthdate' => $request->birthdate,
            'security_answer' => $request->security_answer,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $cat,
        ];
        $user = User::class::create($attributes);

        switch ($category){
            case 'admins':
                $attributes2 = ['user_id' => $user->id,];
                $user2 = Admin::class::create($attributes2);
                break;
            case 'students':
                $attributes2 = ['user_id' => $user->id,'active'=> true,];
                $user2 = Student::class::create($attributes2);
                break;
            case 'teachers':
                $attributes2 = ['user_id' => $user->id,'active'=> true,];
                $user2 = Teacher::class::create($attributes2);
                break;

        }

        return redirect()->route('liste-utilisateurs', ['type' => $category])->with('success', 'user created successfully.');
    }
}
