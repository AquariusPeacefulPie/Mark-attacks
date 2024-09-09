<?php

namespace Tests\Unit\Tests\Unit\Http\Controllers\Admin;

use App\Http\Controllers\Admin\AllUsersController;
use App\Models\Admin;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\View\View;
use Tests\TestCase;


class AllUserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function initiateUser(bool $isAdmin)
    {
        $userDataAdmin = [
            'firstname' => 'Test',
            'lastname' => 'Man',
            'birthdate' => '1990-01-15',
            'email' => 'test@email.com',
            'password' => 'testPassword',
            'role' => 'admin',
            'security_answer'=>'OUI'
        ];

        $userDataStudent = [
            'firstname' => 'Test',
            'lastname' => 'Man',
            'birthdate' => '1990-01-15',
            'email' => 'test@email.com',
            'password' => 'testPassword',
            'role' => 'student',
            'security_answer'=>'OUI'
        ];

        if ($isAdmin)
            $user = User::class::create($userDataAdmin);
        else
            $user = User::class::create($userDataStudent);

        Auth::login($user);
    }

    public function testListeUtilisateursAsAdmin()
    {
        $this->initiateUser(true);

        $response = $this->get('/liste-utilisateurs/admins');
        $response->assertStatus(200);

        $response = $this->get('/liste-utilisateurs/teachers');
        $response->assertStatus(200);

        $response = $this->get('/liste-utilisateurs/students');
        $response->assertStatus(200);

        $response = $this->get('/liste-utilisateurs/users');
        $response->assertStatus(200);

        $response = $this->get('/liste-utilisateurs/inactive%20users');
        $response->assertStatus(200);
    }

    public function test_activate_account_users()
    {
        $user = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'test@test.fr',
            'password' => '12345678',
            'role' => 'student',
            'security_answer' => 'answer'
        ]);

        $students = Student::create(
            [
                'user_id' => $user->id,
                'active' => 1
            ]
        );

        $controller = new AllUsersController();
        $response = $controller->activateAccount('users',$user->id);
        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
    }

    public function test_activate_account_admin()
    {
        $user = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'test@test.fr',
            'password' => '12345678',
            'role' => 'admin',
            'security_answer' => 'answer'
        ]);

        $admin = Admin::create(
            [
                'user_id' => $user->id,
            ]
        );

        $controller = new AllUsersController();
        $response = $controller->activateAccount('admins',$admin->id);
        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
    }

    public function test_activate_account_teacher()
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

        $teachers = Teacher::create(
            [
                'user_id' => $user->id,
                'active' => 1
            ]
        );

        $controller = new AllUsersController();
        $response = $controller->activateAccount('teachers',$teachers->id);
        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
    }

    public function test_activate_account_student()
    {
        $user = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'test@test.fr',
            'password' => '12345678',
            'role' => 'student',
            'security_answer' => 'answer'
        ]);

        $students = Student::create(
            [
                'user_id' => $user->id,
                'active' => 1
            ]
        );

        $controller = new AllUsersController();
        $response = $controller->activateAccount('students',$students->id);
        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
    }

    public function test_activate_account_inactive_users()
    {
        $user = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'test@test.fr',
            'password' => '12345678',
            'role' => 'student',
            'security_answer' => 'answer'
        ]);

        $students = Student::create(
            [
                'user_id' => $user->id,
                'active' => 0
            ]
        );

        $controller = new AllUsersController();
        $response = $controller->activateAccount('inactive users',$user->id);
        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
    }

    public function test_get_inactive_users()
    {
        $user = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'test@test.fr',
            'password' => '12345678',
            'role' => 'student',
            'security_answer' => 'answer'
        ]);

        $student = Student::create(
            [
                'user_id' => $user->id,
                'active' => 0
            ]
        );

        $user2 = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'test2@test.fr',
            'password' => '12345678',
            'role' => 'teacher',
            'security_answer' => 'answer'
        ]);

        $teacher = Student::create(
            [
                'user_id' => $user2->id,
                'active' => 0
            ]
        );

        $controller = new AllUsersController();
        $response = $controller->getInactiveUsers();
        $this->assertInstanceOf(View::class,$response);
        $this->assertEquals('dashboard',$response->name());
    }

    public function test_show_dashboard_admin()
    {
        $user = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'test@test.fr',
            'password' => '12345678',
            'role' => 'admin',
            'security_answer' => 'answer'
        ]);

        $admin = Admin::create(
            [
                'user_id' => $user->id,
            ]
        );

        $this->actingAs($user);

        $controller = new AllUsersController();
        $response = $controller->showDashboard();
        $this->assertInstanceOf(View::class,$response);
        $this->assertEquals('dashboard',$response->name());
    }

    public function test_show_dashboard_teacher()
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

        $teachers = Teacher::create(
            [
                'user_id' => $user->id,
                'active' => 1
            ]
        );

        $this->actingAs($user);

        $controller = new AllUsersController();
        $response = $controller->showDashboard();
        $this->assertInstanceOf(View::class,$response);
        $this->assertEquals('dashboard',$response->name());
    }

    public function test_show_dashboard_student()
    {
        $user = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'test@test.fr',
            'password' => '12345678',
            'role' => 'student',
            'security_answer' => 'answer'
        ]);

        $students = Student::create(
            [
                'user_id' => $user->id,
                'active' => 1
            ]
        );

        $this->actingAs($user);

        $controller = new AllUsersController();
        $response = $controller->showDashboard();
        $this->assertInstanceOf(View::class,$response);
        $this->assertEquals('dashboard',$response->name());
    }


    public function test_destroy_users()
    {
        $user = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'test@test.fr',
            'password' => '12345678',
            'role' => 'student',
            'security_answer' => 'answer'
        ]);

        $students = Student::create(
            [
                'user_id' => $user->id,
                'active' => 1
            ]
        );

        $controller = new AllUsersController();
        $response = $controller->destroy('users',$user->id);
        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
    }

    public function test_destroy_admin()
    {
        $user = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'test@test.fr',
            'password' => '12345678',
            'role' => 'admin',
            'security_answer' => 'answer'
        ]);

        $admin = Admin::create(
            [
                'user_id' => $user->id,
            ]
        );

        $controller = new AllUsersController();
        $response = $controller->destroy('admins',$admin->id);
        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
    }

    public function test_destroy_teacher()
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

        $teachers = Teacher::create(
            [
                'user_id' => $user->id,
                'active' => 1
            ]
        );

        $controller = new AllUsersController();
        $response = $controller->destroy('teachers',$teachers->id);
        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
    }

    public function test_destroy_student()
    {
        $user = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'test@test.fr',
            'password' => '12345678',
            'role' => 'student',
            'security_answer' => 'answer'
        ]);

        $students = Student::create(
            [
                'user_id' => $user->id,
                'active' => 1
            ]
        );

        $controller = new AllUsersController();
        $response = $controller->destroy('students',$students->id);
        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
    }

    public function test_update_user_profil_admin()
    {
        $user = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'test@test.fr',
            'password' => '12345678',
            'role' => 'admin',
            'security_answer' => 'answer'
        ]);

        $admin = Admin::create(
            [
                'user_id' => $user->id,
            ]
        );

        $controller = new AllUsersController();
        $response = $controller->editAdmin('admins',$admin->id);
        $this->assertInstanceOf(View::class,$response);
        $this->assertEquals('profile.edit-admin',$response->name());
    }

    public function test_update_user_profil_teacher()
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

        $teachers = Teacher::create(
            [
                'user_id' => $user->id,
                'active' => 1
            ]
        );

        $controller = new AllUsersController();
        $response = $controller->editAdmin('teachers',$teachers->id);
        $this->assertInstanceOf(View::class,$response);
        $this->assertEquals('profile.edit-admin',$response->name());
    }

    public function test_update_user_profil_student()
    {
        $user = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'test@test.fr',
            'password' => '12345678',
            'role' => 'student',
            'security_answer' => 'answer'
        ]);

        $students = Student::create(
            [
                'user_id' => $user->id,
                'active' => 1
            ]
        );

        $controller = new AllUsersController();
        $response = $controller->editAdmin('students',$students->id);
        $this->assertInstanceOf(View::class,$response);
        $this->assertEquals('profile.edit-admin',$response->name());
    }

    public function test_update_user_profil_other()
    {
        $user = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'test@test.fr',
            'password' => '12345678',
            'role' => 'student',
            'security_answer' => 'answer'
        ]);

        $students = Student::create(
            [
                'user_id' => $user->id,
                'active' => 1
            ]
        );

        $controller = new AllUsersController();
        $response = $controller->editAdmin('users',$user->id);
        $this->assertInstanceOf(View::class,$response);
        $this->assertEquals('profile.edit-admin',$response->name());
    }

    public function test_update_admin()
    {
        $user = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'test@test.fr',
            'password' => '12345678',
            'role' => 'admin',
            'security_answer' => 'answer'
        ]);

        $admin = Admin::create(
            [
                'user_id' => $user->id,
            ]
        );

        $request = Request::create("liste-utilisateurs/admins/update/".$admin->id,'POST',["firstname"=>"prenom","lastname"=>"nom","birthdate"=>"2000-04-25","email"=>"prenom.nom@mail.fr"]);


        $controller = new AllUsersController();
        $response = $controller->update($request,'admins',$admin->id);
        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
    }

    public function test_update_teacher()
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

        $teacher = Teacher::create(
            [
                'user_id' => $user->id,
                'active'=>1
            ]
        );

        $request = Request::create("liste-utilisateurs/teachers/update/".$teacher->id,'POST',["firstname"=>"prenom","lastname"=>"nom","birthdate"=>"2000-04-25","email"=>"prenom.nom@mail.fr"]);


        $controller = new AllUsersController();
        $response = $controller->update($request,'teachers',$teacher->id);
        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
    }

    public function test_update_student()
    {
        $user = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'test@test.fr',
            'password' => '12345678',
            'role' => 'student',
            'security_answer' => 'answer'
        ]);

        $student = Student::create(
            [
                'user_id' => $user->id,
                'active'=>1
            ]
        );

        $request = Request::create("liste-utilisateurs/students/update/".$student->id,'POST',["firstname"=>"prenom","lastname"=>"nom","birthdate"=>"2000-04-25","email"=>"prenom.nom@mail.fr"]);


        $controller = new AllUsersController();
        $response = $controller->update($request,'students',$student->id);
        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
    }

    public function test_update_other()
    {
        $user = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'test@test.fr',
            'password' => '12345678',
            'role' => 'student',
            'security_answer' => 'answer'
        ]);

        $student = Student::create(
            [
                'user_id' => $user->id,
                'active'=>1
            ]
        );

        $request = Request::create("liste-utilisateurs/users/update/".$user->id,'POST',["firstname"=>"prenom","lastname"=>"nom","birthdate"=>"2000-04-25","email"=>"prenom.nom@mail.fr"]);


        $controller = new AllUsersController();
        $response = $controller->update($request,'users',$user->id);
        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
    }



}
