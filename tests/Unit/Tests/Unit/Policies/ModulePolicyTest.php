<?php

namespace Tests\Unit\Policies;

use App\Models\Admin;
use App\Models\Module;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Policies\ModulePolicy;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ModulePolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_view_any_is_admin()
    {
        $user = User::create([
            'firstname' => 'Jofhn',
            'lastname' => 'Dofe',
            'birthdate' => '1990-01-01',
            'email' => 'jofhn.admin@gmail.com',
            'password' => 'passzword',
            'role' => 'admin',
            'security_answer' => 'OUI'
        ]);

        $adminData = [
            'user_id' => $user->id,
        ];

        $admin = Admin::create($adminData);

        $modulePolicy = new ModulePolicy();
        $is_admin=$modulePolicy->viewAny($user);
        $this->assertTrue($is_admin);
    }

    public function test_view_any_is_not_admin()
    {
        $user = User::create([
            'firstname' => 'Jofhn',
            'lastname' => 'Dofe',
            'birthdate' => '1990-01-01',
            'email' => 'jofhn.admin@gmail.com',
            'password' => 'passzword',
            'role' => 'teacher',
            'security_answer' => 'OUI'
        ]);

        $teacherData = [
            'user_id' => $user->id,
            'active' => 1
        ];

        $teacher = Teacher::create($teacherData);

        $modulePolicy = new ModulePolicy();
        $is_admin=$modulePolicy->viewAny($user);
        $this->assertFalse($is_admin);
    }

    public function test_view_is_teacher()
    {
        $user = User::create([
            'firstname' => 'Jofhn',
            'lastname' => 'Dofe',
            'birthdate' => '1990-01-01',
            'email' => 'jofhn.admin@gmail.com',
            'password' => 'passzword',
            'role' => 'teacher',
            'security_answer' => 'OUI'
        ]);

        $teacherData = [
            'user_id' => $user->id,
            'active' => 1
        ];

        $teacher = Teacher::create($teacherData);

        $moduleData = [
            'name' => 'Module Test',
            'coefficient' => 5,
            'user_id'=> $user->id
        ];


        $module = Module::create($moduleData);

        $modulePolicy = new ModulePolicy();
        $can_view=$modulePolicy->view($user,$module);
        $this->assertTrue($can_view);
    }

    public function test_view_is_student()
    {
        $user2 = User::create([
            'firstname' => 'Jofhn',
            'lastname' => 'Dofe',
            'birthdate' => '1990-01-01',
            'email' => 'jofhn2.admin@gmail.com',
            'password' => 'passzword',
            'role' => 'teacher',
            'security_answer' => 'OUI'
        ]);
        $user = User::create([
            'firstname' => 'Jofhn',
            'lastname' => 'Dofe',
            'birthdate' => '1990-01-01',
            'email' => 'jofhn.admin@gmail.com',
            'password' => 'passzword',
            'role' => 'student',
            'security_answer' => 'OUI'
        ]);

        $studentData = [
            'user_id' => $user->id,
            'active' => 1
        ];

        $student = Student::create($studentData);

        $moduleData = [
            'name' => 'Module Test',
            'coefficient' => 5,
            'user_id'=> $user2->id
        ];


        $module = Module::create($moduleData);

        $modulePolicy = new ModulePolicy();
        $can_view=$modulePolicy->view($user,$module);
        $this->assertTrue($can_view);
    }

    public function test_view_is_admin()
    {
        $user2 = User::create([
            'firstname' => 'Jofhn',
            'lastname' => 'Dofe',
            'birthdate' => '1990-01-01',
            'email' => 'jofhn2.admin@gmail.com',
            'password' => 'passzword',
            'role' => 'teacher',
            'security_answer' => 'OUI'
        ]);
        $user = User::create([
            'firstname' => 'Jofhn',
            'lastname' => 'Dofe',
            'birthdate' => '1990-01-01',
            'email' => 'jofhn.admin@gmail.com',
            'password' => 'passzword',
            'role' => 'admin',
            'security_answer' => 'OUI'
        ]);

        $adminData = [
            'user_id' => $user->id,
        ];

        $admin = Student::create($adminData);

        $moduleData = [
            'name' => 'Module Test',
            'coefficient' => 5,
            'user_id'=> $user2->id
        ];


        $module = Module::create($moduleData);

        $modulePolicy = new ModulePolicy();
        $can_view=$modulePolicy->view($user,$module);
        $this->assertTrue($can_view);
    }

    public function test_view_is_not_good_role()
    {
        $user2 = User::create([
            'firstname' => 'Jofhn',
            'lastname' => 'Dofe',
            'birthdate' => '1990-01-01',
            'email' => 'jofhn2.admin@gmail.com',
            'password' => 'passzword',
            'role' => 'teacher',
            'security_answer' => 'OUI'
        ]);
        $user = User::create([
            'firstname' => 'Jofhn',
            'lastname' => 'Dofe',
            'birthdate' => '1990-01-01',
            'email' => 'jofhn.admin@gmail.com',
            'password' => 'passzword',
            'role' => 'badrole',
            'security_answer' => 'OUI'
        ]);


        $moduleData = [
            'name' => 'Module Test',
            'coefficient' => 5,
            'user_id'=> $user2->id
        ];


        $module = Module::create($moduleData);

        $modulePolicy = new ModulePolicy();
        $can_view=$modulePolicy->view($user,$module);
        $this->assertFalse($can_view);
    }

    public function test_create_is_teacher()
    {
        $user = User::create([
            'firstname' => 'Jofhn',
            'lastname' => 'Dofe',
            'birthdate' => '1990-01-01',
            'email' => 'jofhn.admin@gmail.com',
            'password' => 'passzword',
            'role' => 'teacher',
            'security_answer' => 'OUI'
        ]);

        $teacherData = [
            'user_id' => $user->id,
            'active' => 1
        ];

        $teacher = Teacher::create($teacherData);

        $modulePolicy = new ModulePolicy();
        $can_create=$modulePolicy->create($user);
        $this->assertTrue($can_create);
    }

    public function test_create_is_admin()
    {

        $user = User::create([
            'firstname' => 'Jofhn',
            'lastname' => 'Dofe',
            'birthdate' => '1990-01-01',
            'email' => 'jofhn.admin@gmail.com',
            'password' => 'passzword',
            'role' => 'admin',
            'security_answer' => 'OUI'
        ]);

        $adminData = [
            'user_id' => $user->id,
        ];

        $admin = Student::create($adminData);


        $modulePolicy = new ModulePolicy();
        $can_create=$modulePolicy->create($user);
        $this->assertTrue($can_create);
    }

    public function test_create_is_student()
    {
        $user = User::create([
            'firstname' => 'Jofhn',
            'lastname' => 'Dofe',
            'birthdate' => '1990-01-01',
            'email' => 'jofhn.admin@gmail.com',
            'password' => 'passzword',
            'role' => 'student',
            'security_answer' => 'OUI'
        ]);

        $studentData = [
            'user_id' => $user->id,
            'active' => 1
        ];

        $student = Student::create($studentData);

        $modulePolicy = new ModulePolicy();
        $can_create=$modulePolicy->create($user);
        $this->assertFalse($can_create);
    }





    public function test_update_is_teacher()
    {
        $user = User::create([
            'firstname' => 'Jofhn',
            'lastname' => 'Dofe',
            'birthdate' => '1990-01-01',
            'email' => 'jofhn.admin@gmail.com',
            'password' => 'passzword',
            'role' => 'teacher',
            'security_answer' => 'OUI'
        ]);

        $teacherData = [
            'user_id' => $user->id,
            'active' => 1
        ];

        $teacher = Teacher::create($teacherData);

        $moduleData = [
            'name' => 'Module Test',
            'coefficient' => 5,
            'user_id'=> $user->id
        ];


        $module = Module::create($moduleData);

        $modulePolicy = new ModulePolicy();
        $can_view=$modulePolicy->update($user,$module);
        $this->assertTrue($can_view);
    }

    public function test_update_is_student()
    {
        $user2 = User::create([
            'firstname' => 'Jofhn',
            'lastname' => 'Dofe',
            'birthdate' => '1990-01-01',
            'email' => 'jofhn2.admin@gmail.com',
            'password' => 'passzword',
            'role' => 'teacher',
            'security_answer' => 'OUI'
        ]);
        $user = User::create([
            'firstname' => 'Jofhn',
            'lastname' => 'Dofe',
            'birthdate' => '1990-01-01',
            'email' => 'jofhn.admin@gmail.com',
            'password' => 'passzword',
            'role' => 'student',
            'security_answer' => 'OUI'
        ]);

        $studentData = [
            'user_id' => $user->id,
            'active' => 1
        ];

        $student = Student::create($studentData);

        $moduleData = [
            'name' => 'Module Test',
            'coefficient' => 5,
            'user_id'=> $user2->id
        ];


        $module = Module::create($moduleData);

        $modulePolicy = new ModulePolicy();
        $can_view=$modulePolicy->update($user,$module);
        $this->assertTrue($can_view);
    }

    public function test_update_is_admin()
    {
        $user2 = User::create([
            'firstname' => 'Jofhn',
            'lastname' => 'Dofe',
            'birthdate' => '1990-01-01',
            'email' => 'jofhn2.admin@gmail.com',
            'password' => 'passzword',
            'role' => 'teacher',
            'security_answer' => 'OUI'
        ]);
        $user = User::create([
            'firstname' => 'Jofhn',
            'lastname' => 'Dofe',
            'birthdate' => '1990-01-01',
            'email' => 'jofhn.admin@gmail.com',
            'password' => 'passzword',
            'role' => 'admin',
            'security_answer' => 'OUI'
        ]);

        $adminData = [
            'user_id' => $user->id,
        ];

        $admin = Student::create($adminData);

        $moduleData = [
            'name' => 'Module Test',
            'coefficient' => 5,
            'user_id'=> $user2->id
        ];


        $module = Module::create($moduleData);

        $modulePolicy = new ModulePolicy();
        $can_view=$modulePolicy->update($user,$module);
        $this->assertTrue($can_view);
    }

    public function test_update_is_not_good_role()
    {
        $user2 = User::create([
            'firstname' => 'Jofhn',
            'lastname' => 'Dofe',
            'birthdate' => '1990-01-01',
            'email' => 'jofhn2.admin@gmail.com',
            'password' => 'passzword',
            'role' => 'teacher',
            'security_answer' => 'OUI'
        ]);
        $user = User::create([
            'firstname' => 'Jofhn',
            'lastname' => 'Dofe',
            'birthdate' => '1990-01-01',
            'email' => 'jofhn.admin@gmail.com',
            'password' => 'passzword',
            'role' => 'badrole',
            'security_answer' => 'OUI'
        ]);


        $moduleData = [
            'name' => 'Module Test',
            'coefficient' => 5,
            'user_id'=> $user2->id
        ];


        $module = Module::create($moduleData);

        $modulePolicy = new ModulePolicy();
        $can_view=$modulePolicy->update($user,$module);
        $this->assertFalse($can_view);
    }































    public function test_delete_is_teacher()
    {
        $user2 = User::create([
            'firstname' => 'Jofhn',
            'lastname' => 'Dofe',
            'birthdate' => '1990-01-01',
            'email' => 'jofhn2.admin@gmail.com',
            'password' => 'passzword',
            'role' => 'teacher',
            'security_answer' => 'OUI'
        ]);

        $moduleData = [
            'name' => 'Module Test',
            'coefficient' => 5,
            'user_id'=> $user2->id
        ];
        $module = Module::create($moduleData);
        $user = User::create([
            'firstname' => 'Jofhn',
            'lastname' => 'Dofe',
            'birthdate' => '1990-01-01',
            'email' => 'jofhn.admin@gmail.com',
            'password' => 'passzword',
            'role' => 'teacher',
            'security_answer' => 'OUI'
        ]);

        $teacherData = [
            'user_id' => $user->id,
            'active' => 1
        ];

        $teacher = Teacher::create($teacherData);

        $modulePolicy = new ModulePolicy();
        $can_create=$modulePolicy->delete($user,$module);
        $this->assertTrue($can_create);
    }

    public function test_delete_is_admin()
    {
        $user2 = User::create([
            'firstname' => 'Jofhn',
            'lastname' => 'Dofe',
            'birthdate' => '1990-01-01',
            'email' => 'jofhn2.admin@gmail.com',
            'password' => 'passzword',
            'role' => 'teacher',
            'security_answer' => 'OUI'
        ]);

        $moduleData = [
            'name' => 'Module Test',
            'coefficient' => 5,
            'user_id'=> $user2->id
        ];
        $module = Module::create($moduleData);
        $user = User::create([
            'firstname' => 'Jofhn',
            'lastname' => 'Dofe',
            'birthdate' => '1990-01-01',
            'email' => 'jofhn.admin@gmail.com',
            'password' => 'passzword',
            'role' => 'admin',
            'security_answer' => 'OUI'
        ]);

        $adminData = [
            'user_id' => $user->id,
        ];

        $admin = Student::create($adminData);


        $modulePolicy = new ModulePolicy();
        $can_create=$modulePolicy->delete($user,$module);
        $this->assertTrue($can_create);
    }

    public function test_delete_is_student()
    {
        $user2 = User::create([
            'firstname' => 'Jofhn',
            'lastname' => 'Dofe',
            'birthdate' => '1990-01-01',
            'email' => 'jofhn2.admin@gmail.com',
            'password' => 'passzword',
            'role' => 'teacher',
            'security_answer' => 'OUI'
        ]);

        $moduleData = [
            'name' => 'Module Test',
            'coefficient' => 5,
            'user_id'=> $user2->id
        ];
        $module = Module::create($moduleData);
        $user = User::create([
            'firstname' => 'Jofhn',
            'lastname' => 'Dofe',
            'birthdate' => '1990-01-01',
            'email' => 'jofhn.admin@gmail.com',
            'password' => 'passzword',
            'role' => 'student',
            'security_answer' => 'OUI'
        ]);

        $studentData = [
            'user_id' => $user->id,
            'active' => 1
        ];

        $student = Student::create($studentData);

        $modulePolicy = new ModulePolicy();
        $can_create=$modulePolicy->delete($user,$module);
        $this->assertFalse($can_create);
    }

    public function test_restore_is_teacher()
    {
        $user2 = User::create([
            'firstname' => 'Jofhn',
            'lastname' => 'Dofe',
            'birthdate' => '1990-01-01',
            'email' => 'jofhn2.admin@gmail.com',
            'password' => 'passzword',
            'role' => 'teacher',
            'security_answer' => 'OUI'
        ]);

        $moduleData = [
            'name' => 'Module Test',
            'coefficient' => 5,
            'user_id'=> $user2->id
        ];
        $module = Module::create($moduleData);
        $user = User::create([
            'firstname' => 'Jofhn',
            'lastname' => 'Dofe',
            'birthdate' => '1990-01-01',
            'email' => 'jofhn.admin@gmail.com',
            'password' => 'passzword',
            'role' => 'teacher',
            'security_answer' => 'OUI'
        ]);

        $teacherData = [
            'user_id' => $user->id,
            'active' => 1
        ];

        $teacher = Teacher::create($teacherData);

        $modulePolicy = new ModulePolicy();
        $can_create=$modulePolicy->restore($user,$module);
        $this->assertTrue($can_create);
    }

    public function test_restore_is_admin()
    {
        $user2 = User::create([
            'firstname' => 'Jofhn',
            'lastname' => 'Dofe',
            'birthdate' => '1990-01-01',
            'email' => 'jofhn2.admin@gmail.com',
            'password' => 'passzword',
            'role' => 'teacher',
            'security_answer' => 'OUI'
        ]);

        $moduleData = [
            'name' => 'Module Test',
            'coefficient' => 5,
            'user_id'=> $user2->id
        ];
        $module = Module::create($moduleData);
        $user = User::create([
            'firstname' => 'Jofhn',
            'lastname' => 'Dofe',
            'birthdate' => '1990-01-01',
            'email' => 'jofhn.admin@gmail.com',
            'password' => 'passzword',
            'role' => 'admin',
            'security_answer' => 'OUI'
        ]);

        $adminData = [
            'user_id' => $user->id,
        ];

        $admin = Student::create($adminData);


        $modulePolicy = new ModulePolicy();
        $can_create=$modulePolicy->restore($user,$module);
        $this->assertTrue($can_create);
    }

    public function test_restore_is_student()
    {
        $user2 = User::create([
            'firstname' => 'Jofhn',
            'lastname' => 'Dofe',
            'birthdate' => '1990-01-01',
            'email' => 'jofhn2.admin@gmail.com',
            'password' => 'passzword',
            'role' => 'teacher',
            'security_answer' => 'OUI'
        ]);

        $moduleData = [
            'name' => 'Module Test',
            'coefficient' => 5,
            'user_id'=> $user2->id
        ];
        $module = Module::create($moduleData);
        $user = User::create([
            'firstname' => 'Jofhn',
            'lastname' => 'Dofe',
            'birthdate' => '1990-01-01',
            'email' => 'jofhn.admin@gmail.com',
            'password' => 'passzword',
            'role' => 'student',
            'security_answer' => 'OUI'
        ]);

        $studentData = [
            'user_id' => $user->id,
            'active' => 1
        ];

        $student = Student::create($studentData);

        $modulePolicy = new ModulePolicy();
        $can_create=$modulePolicy->restore($user,$module);
        $this->assertFalse($can_create);
    }

    public function test_force_delete_is_teacher()
    {
        $user2 = User::create([
            'firstname' => 'Jofhn',
            'lastname' => 'Dofe',
            'birthdate' => '1990-01-01',
            'email' => 'jofhn2.admin@gmail.com',
            'password' => 'passzword',
            'role' => 'teacher',
            'security_answer' => 'OUI'
        ]);

        $moduleData = [
            'name' => 'Module Test',
            'coefficient' => 5,
            'user_id'=> $user2->id
        ];
        $module = Module::create($moduleData);
        $user = User::create([
            'firstname' => 'Jofhn',
            'lastname' => 'Dofe',
            'birthdate' => '1990-01-01',
            'email' => 'jofhn.admin@gmail.com',
            'password' => 'passzword',
            'role' => 'teacher',
            'security_answer' => 'OUI'
        ]);

        $teacherData = [
            'user_id' => $user->id,
            'active' => 1
        ];

        $teacher = Teacher::create($teacherData);

        $modulePolicy = new ModulePolicy();
        $can_create=$modulePolicy->forceDelete($user,$module);
        $this->assertTrue($can_create);
    }

    public function test_force_delete_is_admin()
    {
        $user2 = User::create([
            'firstname' => 'Jofhn',
            'lastname' => 'Dofe',
            'birthdate' => '1990-01-01',
            'email' => 'jofhn2.admin@gmail.com',
            'password' => 'passzword',
            'role' => 'teacher',
            'security_answer' => 'OUI'
        ]);

        $moduleData = [
            'name' => 'Module Test',
            'coefficient' => 5,
            'user_id'=> $user2->id
        ];
        $module = Module::create($moduleData);
        $user = User::create([
            'firstname' => 'Jofhn',
            'lastname' => 'Dofe',
            'birthdate' => '1990-01-01',
            'email' => 'jofhn.admin@gmail.com',
            'password' => 'passzword',
            'role' => 'admin',
            'security_answer' => 'OUI'
        ]);

        $adminData = [
            'user_id' => $user->id,
        ];

        $admin = Student::create($adminData);


        $modulePolicy = new ModulePolicy();
        $can_create=$modulePolicy->forceDelete($user,$module);
        $this->assertTrue($can_create);
    }

    public function test_force_delete_is_student()
    {
        $user2 = User::create([
            'firstname' => 'Jofhn',
            'lastname' => 'Dofe',
            'birthdate' => '1990-01-01',
            'email' => 'jofhn2.admin@gmail.com',
            'password' => 'passzword',
            'role' => 'teacher',
            'security_answer' => 'OUI'
        ]);

        $moduleData = [
            'name' => 'Module Test',
            'coefficient' => 5,
            'user_id'=> $user2->id
        ];
        $module = Module::create($moduleData);
        $user = User::create([
            'firstname' => 'Jofhn',
            'lastname' => 'Dofe',
            'birthdate' => '1990-01-01',
            'email' => 'jofhn.admin@gmail.com',
            'password' => 'passzword',
            'role' => 'student',
            'security_answer' => 'OUI'
        ]);

        $studentData = [
            'user_id' => $user->id,
            'active' => 1
        ];

        $student = Student::create($studentData);

        $modulePolicy = new ModulePolicy();
        $can_create=$modulePolicy->forceDelete($user,$module);
        $this->assertFalse($can_create);
    }


}
