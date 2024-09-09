<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Teacher extends User
{
    use HasFactory;
    protected $guard = 'teacher';

    public $fillable = [
        'user_id',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function teacher_modules()
    {
        return $this->belongsToMany(TeacherModule::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function modules(){
        return $this->hasMany(Module::class);
    }

    public function getModelType()
    {
        return 'teacher';
    }
}
