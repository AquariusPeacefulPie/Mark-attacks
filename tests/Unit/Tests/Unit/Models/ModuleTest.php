<?php

namespace Tests\Unit\Models;

use App\Models\Exam;
use App\Models\Module;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\TeacherModule;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;
use function PHPUnit\Framework\assertEquals;

class ModuleTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic test example.
     */
    public function test_create_module()
    {
        $user = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'test@test.fr',
            'password' => '12345678',
            'role' => 'teacher',
            'security_answer' => 'answer'
        ]);
        $moduleData = [
            'name' => 'TF',
            'coefficient' => '6',
            'user_id'=>$user->id
        ];

        $module = Module::create($moduleData);

        $this->assertInstanceOf(Module::class, $module);
        $this->assertEquals('TF', $module->name);
        $this->assertEquals('6', $module->coefficient);
    }

    public function test_module_database_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('modules', [
                'id','name', 'coefficient'
            ]), 1);
    }

    public function test_teacher_module_relation()
    {
        $module = new Module();
        $bel = $module->teacher_modules();
        $this->assertInstanceOf(BelongsToMany::class,$bel);

    }

    public function test_exams_relation()
    {
        $module = new Module();
        $bel = $module->exams();
        $this->assertInstanceOf(HasMany::class,$bel);

    }

    public function test_teacher_relation()
    {
        $module = new Module();
        $bel = $module->teacher();
        $this->assertInstanceOf(BelongsTo::class,$bel);

    }

    public function test_results_relation()
    {
        $module = new Module();
        $bel = $module->results();
        $this->assertInstanceOf(HasMany::class,$bel);

    }

    public function test_scopeCalculateAverageMark()
    {
        $user = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'test@test.fr',
            'password' => '12345678',
            'role' => 'teacher',
            'security_answer' => 'answer'
        ]);
        $moduleData = [
            'name' => 'TF',
            'coefficient' => '6',
            'user_id'=>$user->id
        ];

        $module = Module::create($moduleData);
        $module = new Module();
        $average_mark = $module->scopeCalculateAverageMark(Module::query());
        $this->assertEquals(0,$average_mark);

    }

    public function test_scopeCalculateAverageMarkByStudent()
    {
        $user = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'test@test.fr',
            'password' => '12345678',
            'role' => 'teacher',
            'security_answer' => 'answer'
        ]);

        $user2 = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'test2@test.fr',
            'password' => '12345678',
            'role' => 'student',
            'security_answer' => 'answer'
        ]);

        $student = Student::create([
            'user_id'=>$user2->id,
            'active'=>1
        ]);
        $moduleData = [
            'name' => 'TF',
            'coefficient' => '6',
            'user_id'=>$user->id
        ];

        $module = Module::create($moduleData);
        $module = new Module();
        $average_mark = $module->scopeCalculateAverageMarkByStudent(Module::query(),$student->id);
        $this->assertEquals(0,$average_mark);

    }


}
