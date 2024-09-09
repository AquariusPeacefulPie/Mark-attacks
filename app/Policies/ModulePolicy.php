<?php

namespace App\Policies;

use App\Models\Module;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Auth\Access\Response;

class ModulePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny($user): bool
    {
        if($user->role === "admin" ){
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view($user, Module $module): bool
    {
        if($user->role === 'teacher' || $user->role === "admin" || $user->role === "student"){
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create($user): bool
    {
        if($user->role === 'teacher' || $user->role === "admin"){
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update($user, Module $module): bool
    {

        if($user->role === 'teacher' || $user->role === "admin" || $user->role === "student"){
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete($user, Module $module): bool
    {
        if($user->role === 'teacher' || $user->role === "admin"){
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore($user, Module $module): bool
    {
        if($user->role === 'teacher' || $user->role === "admin"){
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete($user, Module $module): bool
    {
        if($user->role === 'teacher' || $user->role === "admin"){
            return true;
        }

        return false;
    }
}
