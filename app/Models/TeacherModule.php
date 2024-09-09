<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherModule extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'teacher_id',
        'module_id',
        'active'
    ];

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class);
    }

    public function modules()
    {
        return $this->belongsToMany(Module::class);
    }

}
