<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentModule extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'module_id',
        'active'
    ];

    public function students()
    {
        return $this->belongsToMany(Student::class);
    }
/*
    public function modules()
    {
        return $this->belongsToMany(Module::class);
    }
*/

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function results()
    {
        return $this->hasMany(Result::class, 'module_id', 'id');
    }





}
