<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ForgotPasswordController extends Controller
{
    public function showResetIntermediateForm()
    {
        return view('auth.reset-password-intermediate');
    }





    public function checkSecret(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'security_answer' => 'required',
        ]);

        $user = User::where('email', $request->email)
            ->where('security_answer', $request->security_answer)
            ->first();

        if ($user) {
            return view('auth.reset-password', ['email' => $request->email]);
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => __('Les informations fournies ne correspondent pas à aucun de nos utilisateurs.')]);
    }

    public function resetFinal(Request $request)
    {   //dump('dump1');
        $request->validate([
            'password' => 'required|min:8',
        ]);
        //dump('dump2');
        $user = User::where('email', $request->email)->first();
        //dump('dump3');
        if ($user) {
            //dump($request->password , $request->password_confirmation , $request->password == $request->password_confirmation);
            if ($request->password == $request->password_confirmation) {
                $user->password = Hash::make($request->password);
                $user->save();
                return redirect()->route('login')->with('status', __('Le mot de passe a été réinitialisé avec succès.'));
            } else {
                //dump('dump else');
                return redirect()->route('login')->with('error', __('Le mot de passe et sa confirmation ne correspondent pas, vous devez recommencer.'));
                //return back()->withInput()->withErrors(['password' => 'Les mots de passe ne correspondent pas']);
                //return redirect()->back()->withErrors('Le mot de passe et sa confirmation ne correspondent pas.refaites le process')->withInput();
            }
        }

    }



}
