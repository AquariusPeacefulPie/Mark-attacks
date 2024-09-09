<?php
namespace Tests\Unit\Models;
use App\Models\Admin;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TeacherTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_teacher()
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

        $this->assertInstanceOf(Teacher::class, $teacher);
        $this->assertEquals($user->id, $teacher->user_id);
    }

    public function test_teacher_database_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('teachers', [
                'id','user_id','active'
            ]), 1);
    }

    public function test_get_Model_Type()
    {
        $teacher = new Teacher();
        $this->assertEquals("teacher",$teacher->getModelType());
    }

    public function test_user_relation()
    {
        $teacher = new Teacher();
        $bel = $teacher->user();
        $this->assertInstanceOf(BelongsTo::class,$bel);

    }

    public function test_teacher_module_relation()
    {
        $teacher = new Teacher();
        $bel = $teacher->teacher_modules();
        $this->assertInstanceOf(BelongsToMany::class,$bel);

    }

    public function test_modules_relation()
    {
        $teacher = new Teacher();
        $bel = $teacher->modules();
        $this->assertInstanceOf(HasMany::class,$bel);

    }
}
