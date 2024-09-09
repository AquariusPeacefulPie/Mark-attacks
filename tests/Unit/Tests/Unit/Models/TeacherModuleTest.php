<?php

namespace Tests\Unit\Models;

use App\Models\Admin;
use App\Models\Module;
use App\Models\Teacher;
use App\Models\TeacherModule;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TeacherModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_teacher_module()
    {
        $user = User::create([
            'firstname' => 'Jofhn',
            'lastname' => 'Dofe',
            'birthdate' => '1990-01-01',
            'email' => 'jofhn.admin@gmail.com',
            'password' => 'passzword',
            'role' => 'teacher',
            'security_answer'=>'OUI'
        ]);

        $teacherData = [
            'user_id' => $user->id,
            'active' => 1,
        ];

        $teacher = Teacher::create($teacherData);

        $moduleData = [
            'name' => 'TF',
            'coefficient' => '6',
            'user_id' => $user->id,
        ];
        $module = Module::create($moduleData);

        $teacherModule = TeacherModule::create([
            'teacher_id' => $teacher->id,
            'module_id' => $module->id
        ]);



        $this->assertInstanceOf(TeacherModule::class, $teacherModule);
        $this->assertEquals($teacher->id, $teacherModule->teacher_id);
        $this->assertEquals($module->id, $teacherModule->module_id);
    }

    public function test_teacher_module_database_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('teacher_modules', [
                'id', 'teacher_id', 'module_id'
            ]), 1);
    }

    public function test_teacher_relation()
    {
        $teacherModule = new TeacherModule();
        $bel = $teacherModule->teachers();
        $this->assertInstanceOf(BelongsToMany::class, $bel);

    }

    public function test_module_relation()
    {
        $teacherModule = new TeacherModule();
        $bel = $teacherModule->modules();
        $this->assertInstanceOf(BelongsToMany::class, $bel);

    }
}
