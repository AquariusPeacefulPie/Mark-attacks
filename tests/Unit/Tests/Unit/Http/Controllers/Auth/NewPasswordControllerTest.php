<?php
namespace Tests\Unit\Http\Controllers\Auth;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Student;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Tests\TestCase;

class NewPasswordControllerTest extends TestCase
{
    use RefreshDatabase;

    private Student $student;
    protected function setUp(): void
    {
        parent::setUp();

        $user = User::create([
            'firstname' => 'Jofhn',
            'lastname' => 'Dofe',
            'birthdate' => '1990-01-01',
            'email' => 'jofhn.dofe@gmail.com',
            'password' => 'passzword',
            'role' => 'student',
            'security_answer'=>'OUI'
        ]);

        $this->student = Student::create(
            [
                'user_id' => $user->id,
                'active' => 1,
                'average_mark' => 15
            ]
        );

    }

    public function test_create_valid()
    {



        $request = Request::create('/confirm-password', 'POST', [
            'email' => 'jofhn.dofe@gmail.com',
            'password' => 'passzword',
        ]);

        $controller = new NewPasswordController();
        $view = $controller->create($request);
        $this->assertInstanceOf(View::class, $view);
        $this->assertEquals('auth.reset-password', $view->getName());
    }

}

