<?php
namespace Tests\Unit\Models;
use App\Models\Admin;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class StudentTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_student()
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

        $this->assertInstanceOf(Student::class, $student);
        $this->assertEquals($user->id, $student->user_id);
    }

    public function test_student_database_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('students', [
                'id','user_id','active'
            ]), 1);
    }

    public function test_get_Model_Type()
    {
        $student = new Student();
        $this->assertEquals("student",$student->getModelType());
    }

    public function test_results_relation()
    {
        $student = new Student();
        $bel = $student->results();
        $this->assertInstanceOf(HasMany::class,$bel);

    }

    public function test_user_relation()
    {
        $student = new Student();
        $bel = $student->user();
        $this->assertInstanceOf(BelongsTo::class,$bel);

    }
}
