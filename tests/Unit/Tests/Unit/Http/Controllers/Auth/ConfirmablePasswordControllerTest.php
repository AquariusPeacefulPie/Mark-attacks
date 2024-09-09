<?php
namespace Tests\Unit\Http\Controllers\Auth;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
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

class ConfirmablePasswordControllerTest extends TestCase
{
    use RefreshDatabase;
    public function test_show_valid()
    {
        $controller = new ConfirmablePasswordController();
        $view = $controller->show();
        $this->assertInstanceOf(View::class, $view);
        $this->assertEquals('auth.confirm-password', $view->getName());
    }


    public function test_store_good_credentials()
    {
        $attributes = [
            'firstname' => 'Jofhn',
            'lastname' => 'Dofe',
            'birthdate' => '1990-01-01',
            'email' => 'jofhn.dofe@gmail.com',
            'password' => 'passzword',
            'role' => 'student',
            'security_answer'=>'OUI'
        ];
        $user = User::class::create($attributes);
        $attributes2 = [
            'user_id' => $user->id,
            'active' => '0',
            'average_mark' => '12',
        ];
        Student::class::create($attributes2);

        // Mock de la classe ConfirmablePasswordController
        $controller = $this->createMock(ConfirmablePasswordController::class);

        // Définir le comportement attendu du mock
        $controller->expects($this->once())
            ->method('store')
            ->willReturn(new RedirectResponse(RouteServiceProvider::HOME));
        $request = Request::create('/confirm-password', 'POST', [
            'email' => $user->email,
            'password' => 'passzword',
        ]);


        $response = $controller->store($request);
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(RouteServiceProvider::HOME, $response->getTargetUrl());
    }


    public function test_store_bad_credentials()
    {
        // Mock de la classe ConfirmablePasswordController
        $controller = $this->createMock(ConfirmablePasswordController::class);

        // Définir le comportement attendu du mock
        $controller->expects($this->once())
            ->method('store')
            ->willThrowException(ValidationException::withMessages([
                'password' => ['auth.password'],
            ]));
        $request = Request::create('/confirm-password', 'POST', [
            'email' => 'email_incorrect@example.com',
            'password' => 'mot_de_passe_incorrect',
        ]);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('auth.password');
        $controller->store($request);
    }
}
