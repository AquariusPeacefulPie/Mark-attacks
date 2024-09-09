<?php

namespace Tests\Unit\Tests\Unit\Http\Controllers;

use App\Http\Controllers\Admin\CreateAdminController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\HistogramController;
use App\Models\Exam;
use App\Models\Module;
use App\Models\Result;
use App\Models\Student;
use App\Models\StudentModule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Tests\TestCase;
use Tests\Unit\Tests\Unit\Http\Controllers\Admin\AllUserControllerTest;

class ForgotPasswordControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_reset_intermediate_form()
    {
        $controller = new ForgotPasswordController();
        $response = $controller->showResetIntermediateForm();
        $this->assertInstanceOf(View::class,$response);
        $this->assertEquals('auth.reset-password-intermediate',$response->name());
    }

    public function test_check_secret_correct_secret() {
        $controller = new ForgotPasswordController();
        User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'test@test.fr',
            'password' => '12345678',
            'role' => 'student',
            'security_answer' => 'answer'
        ]);

        $requestData = [
          'email' => 'test@test.fr',
          'security_answer' => 'answer'
        ];

        $request = new Request($requestData);

        $response = $controller->checkSecret($request);

        $this->assertInstanceOf(View::class, $response);
        $this->assertEquals('auth.reset-password', $response->getName());
        $this->assertEquals('test@test.fr', $response->getData()['email']);
    }

    public function test_check_secret_incorrect_secret() {
        $controller = new ForgotPasswordController();
        User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'test@test.fr',
            'password' => '12345678',
            'role' => 'student',
            'security_answer' => 'answer'
        ]);

        $requestData = [
            'email' => 'test@test.fr',
            'security_answer' => 'yolo'
        ];

        $request = new Request($requestData);

        $response = $controller->checkSecret($request);

        $this->assertTrue($response->isRedirect());
    }

    public function test_check_secret_unknown_user() {
        $controller = new ForgotPasswordController();
        $requestData = [
            'email' => 'test@test.fr',
            'security_answer' => 'answer'
        ];

        $request = new Request($requestData);

        $response = $controller->checkSecret($request);

        $this->assertTrue($response->isRedirect());
    }

    public function test_reset_final_correct() {
        $controller = new ForgotPasswordController();
        User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'test@test.fr',
            'password' => '12345678',
            'role' => 'student',
            'security_answer' => 'answer'
        ]);

        $requestData = [
            'password' => '12345678',
            'password_confirmation' => '12345678',
            'email' => 'test@test.fr'
        ];

        $request = new Request($requestData);

        $response = $controller->resetFinal($request);
        $this->assertTrue($response->isRedirect());
    }

    public function test_reset_final_unknown_user() {
        $controller = new ForgotPasswordController();

        $requestData = [
            'password' => '12345678',
            'password_confirmation' => '12345678',
            'email' => 'test@test.fr'
        ];

        $request = new Request($requestData);

        $response = $controller->resetFinal($request);
        $this->assertNull($response);
    }

    public function test_reset_final_different_passwords() {
        $controller = new ForgotPasswordController();
        User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'test@test.fr',
            'password' => '12345678',
            'role' => 'student',
            'security_answer' => 'answer'
        ]);

        $requestData = [
            'password' => '12345678',
            'password_confirmation' => '87654321',
            'email' => 'test@test.fr'
        ];

        $request = new Request($requestData);

        $response = $controller->resetFinal($request);
        $this->assertTrue($response->isRedirect());
    }
}
