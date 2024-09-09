<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'coefficient',
        'user_id'
    ];

    public function teacher_modules()
    {
        return $this->belongsToMany(TeacherModule::class);
    }

    public function teacher(){
        return $this->belongsTo(User::class);
    }

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }

    public function scopeCalculateAverageMark($query)
    {
        return $query->join('exams', 'exams.module_id', '=', 'modules.id')
            ->join('results', 'results.exam_id', '=', 'exams.id')
            ->where('modules.id', $this->id)
            ->avg('results.mark') ?? 0;
    }

    public function scopeCalculateAverageMarkByStudent($query, $student_id)
    {
        return $query->join('exams', 'exams.module_id', '=', 'modules.id')
            ->join('results', 'results.exam_id', '=', 'exams.id')
            ->where('modules.id', $this->id)
            ->where('results.student_id', $student_id)
            ->avg('results.mark') ?? 0;
    }
}
