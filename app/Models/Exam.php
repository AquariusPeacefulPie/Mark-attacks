<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'module_id',
        'coefficient',
    ];

    public function modules()
    {
        return $this->belongsTo(Module::class);
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }
}
