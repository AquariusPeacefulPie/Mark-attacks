<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends User
{
    use HasFactory;
    protected $guard = 'student';

    public $fillable = [
        'user_id',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function results()
    {
        return $this->hasMany(Result::class);
    }

    public function getModelType()
    {
        return 'student';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
