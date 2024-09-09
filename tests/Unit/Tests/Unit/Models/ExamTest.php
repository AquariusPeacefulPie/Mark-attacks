<?php
namespace Tests\Unit\Models;
use App\Models\Admin;
use App\Models\Exam;
use App\Models\Module;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ExamTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_exam()
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

        $module = Module::create([
            'name' => 'TF',
            'coefficient' => '6',
            'user_id'=>$user->id
        ]);

        $examData = [
            'name' => 'Test 1',
            'module_id' => $module->id,
            'coefficient' => '2',
        ];

        $exam = Exam::create($examData);

        $this->assertInstanceOf(Exam::class, $exam);
        $this->assertEquals($module->id, $exam->module_id);
    }

    public function test_exam_database_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('exams', [
                'id','name','module_id','coefficient'
            ]), 1);
    }

    public function test_modules_relation()
    {
        $exam = new Exam();
        $bel = $exam->modules();
        $this->assertInstanceOf(BelongsTo::class,$bel);

    }

    public function test_results_relation()
    {
        $exam = new Exam();
        $bel = $exam->results();
        $this->assertInstanceOf(HasMany::class,$bel);

    }
}
