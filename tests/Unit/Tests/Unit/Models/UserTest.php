<?php
namespace Tests\Unit\Models;
use App\Models\Admin;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_user()
    {
        $user = User::create([
            'firstname' => 'Jofhn',
            'lastname' => 'Dofe',
            'birthdate' => '1990-01-01',
            'email' => 'jofhn.admin@gmail.com',
            'password' => 'passzword',
            'role' => 'admin',
            'security_answer'=>'OUI'
        ]);



        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('Jofhn', $user->firstname);
        $this->assertEquals('Dofe', $user->lastname);
        $this->assertEquals('1990-01-01', $user->birthdate);
        $this->assertEquals('jofhn.admin@gmail.com', $user->email);
        $this->assertTrue(Hash::check('passzword', $user->password));
        $this->assertEquals('admin', $user->role);
    }

    public function test_user_database_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('users', [
                'id','firstname','lastname','birthdate','email','password','role','security_answer'
            ]), 1);
    }

    public function test_teacher_relation()
    {
        $user = new User();
        $bel = $user->teacher();
        $this->assertInstanceOf(HasOne::class,$bel);

    }

    public function test_student_relation()
    {
        $user = new User();
        $bel = $user->student();
        $this->assertInstanceOf(HasOne::class,$bel);

    }

    public function test_admin_relation()
    {
        $user = new User();
        $bel = $user->admin();
        $this->assertInstanceOf(HasOne::class,$bel);

    }

    public function test_user_is_active_and_is_admin()
    {
        $user = User::create([
            'firstname' => 'Jofhn',
            'lastname' => 'Dofe',
            'birthdate' => '1990-01-01',
            'email' => 'jofhn.admin@gmail.com',
            'password' => 'passzword',
            'role' => 'admin',
            'security_answer'=>'OUI'
        ]);


        $this->assertEquals(1,$user->isActive());

    }

    public function test_user_is_active_and_is_student()
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
            'average_mark' => 12
        ];

        $student = Student::create($studentData);


        $this->assertEquals(1,$user->isActive());

    }

    public function test_user_is_active_and_is_teacher()
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


        $this->assertEquals(1,$user->isActive());

    }

    public function test_user_is_no_active_and_is_student()
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
            'active' => 0,
            'average_mark' => 12
        ];

        $student = Student::create($studentData);


        $this->assertEquals(0,$user->isActive());

    }

    public function test_user_is_no_active_and_is_teacher()
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
            'active' => 0,
        ];

        $teacher = Teacher::create($teacherData);


        $this->assertEquals(0,$user->isActive());

    }


}
