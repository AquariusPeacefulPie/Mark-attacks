<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{

    public $fillable = [
        'firstname',
        'lastname',
        'birthdate',
        'email',
        'password',
        'role',
        'security_answer',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function teacher(){
        return $this->hasOne(Teacher::class);
    }

    public function student(){
        return $this->hasOne(Student::class);
    }

    public function admin(){
        return $this->hasOne(Admin::class);
    }

    public function isActive()
    {
        if ($this->role == 'admin') {
            return true; // Les admins sont toujours considérés comme actifs
        }

        $table = $this->role . 's';

        $isActive = DB::table($table)
            ->where('user_id', $this->id)
            ->value('active');

        //dump($isActive . 'hhhhhhhhhhhhhhhhhhhhéééééééééééééééééééééyyyyyy');
        return $isActive;
    }

}
