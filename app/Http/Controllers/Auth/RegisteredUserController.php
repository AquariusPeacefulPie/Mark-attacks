<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Spatie\LaravelIgnition\Recorders\DumpRecorder\Dump;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'birthdate' => ['required', 'date'],
            'security_answer' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Vérifier si l'utilisateur est un enseignant
        $isTeacher = $request->has('is_teacher');

        // Choisir le modèle en fonction de l'utilisateur
        $userModel = $isTeacher ? Teacher::class : Student::class;
        $attributes = [
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'birthdate' => $request->birthdate,
            'security_answer' => $request->security_answer,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $isTeacher ? 'teacher' : 'student',
        ];



        $user = User::class::create($attributes);

        $attributes2 = [
            'active' => false,
            'user_id' => $user->id,
        ];
        $user2 = $userModel::create($attributes2);


        event(new Registered($user));
        //Auth::login($user);
        //return redirect(RouteServiceProvider::HOME);
        return redirect()->route('login')->with('success', 'Votre compte a été créé avec succès. Veuillez vous connecter svp.');
    }


}
