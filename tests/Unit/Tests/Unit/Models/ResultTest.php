<?php
namespace Tests\Unit\Models;
use App\Models\Admin;
use App\Models\Exam;
use App\Models\Module;
use App\Models\Result;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ResultTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_result()
    {
        $user = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'test@test.fr',
            'password' => '12345678',
            'role' => 'student',
            'security_answer'=>'OUI'
        ]);

        $student = Student::create(
            [
                'user_id' => $user->id,
                'active' => 1,
            ]
        );

        $module = Module::create(
            [
                'name' => 'test',
                'coefficient' => 6,
                'user_id'=>$user->id
            ]
        );

        $exam = Exam::create(
            [
                'name' => 'test_1',
                'module_id' => $module->id,
                'coefficient' => 3,
            ]
        );

        $result=Result::create([
            'student_id' => $student->id,
            'exam_id' => $exam->id,
            'mark' => 10
        ]);

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals($student->id, $result->student_id);
        $this->assertEquals($exam->id, $result->exam_id);
    }

    public function test_result_database_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('results', [
                'id','student_id','exam_id','mark'
            ]), 1);
    }

    public function test_exams_relation()
    {
        $result = new Result();
        $bel = $result->exams();
        $this->assertInstanceOf(BelongsTo::class,$bel);

    }

    public function test_students_relation()
    {
        $result = new Result();
        $bel = $result->students();
        $this->assertInstanceOf(BelongsTo::class,$bel);

    }

    public function test_module_relation()
    {
        $result = new Result();
        $bel = $result->module();
        $this->assertInstanceOf(BelongsTo::class,$bel);

    }
}
