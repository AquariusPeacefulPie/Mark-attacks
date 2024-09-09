<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Admin extends User
{
    use HasFactory;
    protected $guard = 'admin';

    public $fillable = [
        'user_id',
    ];


    public function getModelType()
    {
        return 'admin';
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
