<?php

namespace Tests\Unit\Tests\Unit\Http\Controllers\Admin;

use App\Http\Controllers\Admin\CreateAdminController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Tests\TestCase;
use Tests\Unit\Tests\Unit\Http\Controllers\Admin\AllUserControllerTest;

class CreateAdminControllerTest extends TestCase
{
    use RefreshDatabase;
    public function initiateUser()
    {
        $userDataAdmin = [
            'firstname' => 'Test',
            'lastname' => 'Man',
            'birthdate' => '1990-01-15',
            'email' => 'test10@email.com',
            'password' => 'testPassword',
            'role' => 'admin',
            'security_answer'=>'OUI'
        ];

        $user = User::class::create($userDataAdmin);
        Auth::login($user);
    }

    public function testCreateAdmin(): void
    {

        $this->initiateUser();

        $newUserData = [
            'firstname' => 'AAA',
            'lastname' => 'BBB',
            'birthdate' => '1990-01-15',
            'email' => 'newAdmin@email.com',
            'password' => 'testPassword',
            'password_confirmation' => 'testPassword',
            'security_answer'=>'OUI'
        ];

        $response = $this->post(route('admin.store', ['category' => 'admins']), $newUserData + ['_token' => csrf_token()]);
        $response->assertRedirect(route('liste-utilisateurs', ['type' => 'admins']));
        $response->assertSessionHas('success', 'user created successfully.');

        $this->assertDatabaseHas('users', [
            'firstname' => 'AAA',
            'lastname' => 'BBB',
            'email' => 'newAdmin@email.com',
            'role' => 'admin',
        ]);

        $this->assertDatabaseHas('admins', [
        ]);

        $this->assertAuthenticated();
    }

    public function test_create_view(): void
    {
        $userDataAdmin = [
            'firstname' => 'Test',
            'lastname' => 'Man',
            'birthdate' => '1990-01-15',
            'email' => 'test10@email.com',
            'password' => 'testPassword',
            'role' => 'admin',
            'security_answer'=>'OUI'
        ];

        $user = User::class::create($userDataAdmin);
        $this->actingAs($user);

        $controller = new CreateAdminController();
        $response=$controller->create('students');

        $this->assertInstanceOf(View::class,$response);
        $this->assertEquals('create-admin',$response->name());
    }

    public function testCreateStudent(): void
    {

        $this->initiateUser();
        $newUserData = [
            'firstname' => 'AAA',
            'lastname' => 'BBB',
            'birthdate' => '1990-01-15',
            'email' => 'newAdmin@email.com',
            'password' => 'testPassword',
            'password_confirmation' => 'testPassword',
            'security_answer'=>'OUI'
        ];

        $response = $this->post(route('admin.store', ['category' => 'students']), $newUserData + ['_token' => csrf_token()]);
        $response->assertRedirect(route('liste-utilisateurs', ['type' => 'students']));
        $response->assertSessionHas('success', 'user created successfully.');

        $this->assertDatabaseHas('users', [
            'firstname' => 'AAA',
            'lastname' => 'BBB',
            'email' => 'newAdmin@email.com',
            'role' => 'student',
        ]);

        $this->assertDatabaseHas('students', [
        ]);

        $this->assertAuthenticated();
    }

    public function testCreateTeacher(): void
    {

        $this->initiateUser();
        $newUserData = [
            'firstname' => 'AAA',
            'lastname' => 'BBB',
            'birthdate' => '1990-01-15',
            'email' => 'newAdmin@email.com',
            'password' => 'testPassword',
            'password_confirmation' => 'testPassword',
            'security_answer'=>'OUI'
        ];

        $response = $this->post(route('admin.store', ['category' => 'teachers']), $newUserData + ['_token' => csrf_token()]);
        $response->assertRedirect(route('liste-utilisateurs', ['type' => 'teachers']));
        $response->assertSessionHas('success', 'user created successfully.');

        $this->assertDatabaseHas('users', [
            'firstname' => 'AAA',
            'lastname' => 'BBB',
            'email' => 'newAdmin@email.com',
            'role' => 'teacher',
        ]);

        $this->assertDatabaseHas('teachers', [
        ]);

        $this->assertAuthenticated();
    }
}
