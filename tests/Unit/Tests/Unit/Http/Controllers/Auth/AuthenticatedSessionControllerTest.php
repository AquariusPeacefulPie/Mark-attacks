<?php
namespace Tests\Unit\Http\Controllers\Auth;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Student;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Session;
use Illuminate\View\View;
use Tests\TestCase;
use Illuminate\Http\Response;

class AuthenticatedSessionControllerTest extends TestCase{
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



        $user2 = User::create([
            'firstname' => 'Jofhn',
            'lastname' => 'Dofe',
            'birthdate' => '1990-01-01',
            'email' => 'jofhn.dofe2@gmail.com',
            'password' => 'passzword',
            'role' => 'student',
            'security_answer'=>'OUI'
        ]);

        $this->student2 = Student::create(
            [
                'user_id' => $user2->id,
                'active' => 0,
                'average_mark' => 12
            ]
        );
    }

    public function test_create_valid()
    {
        $controller = new AuthenticatedSessionController();
        $view = $controller->create();
        $this->assertInstanceOf(View::class, $view);
        $this->assertEquals('auth.login', $view->getName());
    }

   public function test_store_method_redirects_authenticated_user(){
        $controller = new AuthenticatedSessionController();
       $request = $this->mock(LoginRequest::class);

       $request->shouldReceive('only')
           ->withArgs(['email', 'password'])
           ->andReturn([
               'email' => 'jofhn.dofe@gmail.com',
               'password' => 'passzword',
           ]);
       $request->shouldReceive('session')
           ->andReturn($this->app['session.store']);

       $response = $controller->store($request);
       $this->assertInstanceOf(RedirectResponse::class, $response);
       $this->assertEquals('http://localhost'.RouteServiceProvider::HOME, $response->getTargetUrl());


    }

    public function test_store_method_redirects_unauthenticated_user_no_active(){

        $controller = new AuthenticatedSessionController();

        $request = $this->mock(LoginRequest::class);

        $request->shouldReceive('only')
            ->withArgs(['email', 'password'])
            ->andReturn([
                'email' => 'jofhn.dofe2@gmail.com',
                'password' => 'passzword',
            ]);
        $response = $controller->store($request);
        $this->assertStringContainsString('Votre compte n\'est pas activé. Attendez que l\'administrateur active votre compte.', $response->getSession()->get('error'));
        $this->assertInstanceOf(RedirectResponse::class, $response);
    }

    public function test_store_method_redirects_unauthenticated_user_no_registered(){
        $controller = new AuthenticatedSessionController();
        $request = new LoginRequest([
            'email' => 'jofhn.dofe@gmail.com',
            'password' => 'passzworjhud',
        ]);
        $response = $controller->store($request);
        $this->assertStringContainsString('Les informations fournies ne correspondent à aucun utilisateur.',$response->getSession()->get('error'));
        $this->assertInstanceOf(RedirectResponse::class, $response);
    }


    public function test_destroy_method_logs_out_user_and_redirects(){

        $controller = new AuthenticatedSessionController();
        $request = $this->mock(Request::class);

        $request->shouldReceive('session')
            ->andReturn(\session());

        $request->shouldReceive('invalidate')
            ->andReturn(true);


        $response=$controller->destroy($request);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals('http://localhost', $response->getTargetUrl());


    }

}
