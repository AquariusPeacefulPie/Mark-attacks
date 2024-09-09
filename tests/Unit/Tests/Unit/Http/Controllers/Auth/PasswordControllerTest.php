<?php
namespace Tests\Unit\Http\Controllers\Auth;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Student;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Tests\TestCase;

class PasswordControllerTest extends TestCase
{
    use RefreshDatabase;


    public function test_password_update() : void
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
            'active' => '1',
            'average_mark' => '12',
        ];
        Student::class::create($attributes2);
        $request = $this->mock(Request::class);
        $request->shouldReceive('user')->andReturn($user);
        $request->shouldReceive('validateWithBag')
            ->with('updatePassword', [
                'current_password' => ['required', 'current_password'],
                'password' => ['required', Password::defaults(), 'confirmed'],
            ])->andReturn([
                'current_password' => 'passzword',
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);
        $controller = new PasswordController();
        $response=$controller->update($request);
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals('password-updated',$response->getSession()->get("status"));


    }
    /** A REVOIR CAR PROBLEME AVEC SESSION **/
    /*
    public function test_password_update()
    {
        $attributes = [
            'firstname' => 'Jofhn',
            'lastname' => 'Dofe',
            'birthdate' => '1990-01-01',
            'email' => 'jofhn.dofe@gmail.com',
            'password' => Hash::make('passzword'),
            'role' => 'student',
            'security_answer'=>'OUI'
        ];
        $user = User::class::create($attributes);
        $attributes2 = [
            'user_id' => $user->id,
            'active' => '1',
            'average_mark' => '12',
        ];
        Student::class::create($attributes2);

        $this->actingAs($user);

        $request = Request::create('/password', 'POST', [
            'current_password' =>'passzword',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        $controller = new PasswordController();
        $response = $controller->update($request);
        $response->assertRedirect();
        $this->assertTrue(Hash::check('newpassword', $user->fresh()->password));
        $this->assertTrue(session()->has('status'));
    }
    */
}
