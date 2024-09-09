<?php
namespace Tests\Unit\Http\Controllers\Auth;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Illuminate\Http\Response;

class RegisteredUserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_method_returns_view()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertViewIs('auth.register');
    }

    public function test_user_can_register_as_student()
    {
        $userData = [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'birthdate' => '1990-01-01',
            'email' => 'john@example.com',
            'password' => 'password123',
            'security_answer'=>'OUI',
            'password_confirmation' => 'password123',
        ];

        $response = $this->post('/register', $userData);
        $user = User::where('email', 'john@example.com')->first();
        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
        $this->assertDatabaseHas('students', ['user_id' => $user->id]);
    }

    public function test_user_can_register_as_teacher()
    {
        $userData = [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'birthdate' => '1990-01-01',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'is_teacher' => true,
            'security_answer'=>'OUI'
        ];

        $response = $this->post('/register', $userData);
        $user = User::where('email', 'john@example.com')->first();
        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
        $this->assertDatabaseHas('teachers', ['user_id' => $user->id]);
        $this->assertDatabaseEmpty('students');
    }

    public function test_user_cant_register_as_student()
    {
        $userData = [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'birthdate' => '1990-01-01',
            'email' => 'johnexample.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'security_answer'=>'OUI'
        ];

        $this->post('/register', $userData);
        $this->assertDatabaseEmpty('users');
        $this->assertDatabaseEmpty('students');
    }
}

