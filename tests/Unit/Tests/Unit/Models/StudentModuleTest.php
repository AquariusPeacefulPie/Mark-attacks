<?php

namespace Tests\Unit\Models;

use App\Models\Admin;
use App\Models\Module;
use App\Models\Student;
use App\Models\StudentModule;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class StudentModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_student_module()
    {
        $user = User::create([
            'firstname' => 'Jofhn',
            'lastname' => 'Dofe',
            'birthdate' => '1990-01-01',
            'email' => 'jofhn.admin@gmail.com',
            'password' => 'passzword',
            'role' => 'student',
            'security_answer'=>'OUI'
        ]);

        $studentData = [
            'user_id' => $user->id,
            'active' => 1,
        ];

        $student = Student::create($studentData);

        $moduleData = [
            'name' => 'TF',
            'coefficient' => '6',
            'user_id' => $user->id,
        ];
        $module = Module::create($moduleData);

        $studentModule = StudentModule::create([
            'student_id' => $student->id,
            'module_id' => $module->id
        ]);



        $this->assertInstanceOf(StudentModule::class, $studentModule);
        $this->assertEquals($student->id, $studentModule->student_id);
        $this->assertEquals($module->id, $studentModule->module_id);
    }

    public function test_student_module_database_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('student_modules', [
                'id', 'student_id', 'module_id'
            ]), 1);
    }

    public function test_student_relation()
    {
        $studentModule = new StudentModule();
        $bel = $studentModule->students();
        $this->assertInstanceOf(BelongsToMany::class, $bel);

    }

    public function test_module_relation()
    {
        $studentModule = new StudentModule();
        $bel = $studentModule->module();
        $this->assertInstanceOf(BelongsTo::class, $bel);

    }

    public function test_results_relation()
    {
        $studentModule = new StudentModule();
        $bel = $studentModule->results();
        $this->assertInstanceOf(HasMany::class, $bel);

    }
}
