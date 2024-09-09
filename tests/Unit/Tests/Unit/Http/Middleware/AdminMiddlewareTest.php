<?php

namespace Tests\Unit\Tests\Unit\Http\Middleware;

use App\Http\Controllers\Admin\CreateAdminController;
use App\Http\Controllers\HistogramController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\StudentMiddleware;
use App\Http\Middleware\TeacherMiddleware;
use App\Models\Admin;
use App\Models\Exam;
use App\Models\Module;
use App\Models\Result;
use App\Models\Student;
use App\Models\StudentModule;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\View\View;
use Tests\TestCase;
use Tests\Unit\Tests\Unit\Http\Controllers\Admin\AllUserControllerTest;

class AdminMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_handle_valid()
    {
        $request = Request::create('/admin-dashboard', 'GET');
        $user = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'test@test.fr',
            'password' => '12345678',
            'role' => 'admin',
            'security_answer' => 'answer'
        ]);

        $admin = Admin::create([
            'user_id'=>$user->id,
        ]);
        $this->actingAs($user);

        $middleware = new AdminMiddleware();
        $response = $middleware->handle($request, function () {
            return response('Authorized');
        });

        $this->assertEquals('Authorized', $response->getContent());
    }

    public function test_handle_no_valid()
    {
        $request = Request::create('/admin-dashboard', 'GET');
        $user = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'test@test.fr',
            'password' => '12345678',
            'role' => 'student',
            'security_answer' => 'answer'
        ]);

        $student = Student::create([
            'user_id'=>$user->id,
            'active'=>1
        ]);
        $this->actingAs($user);

        $middleware = new AdminMiddleware();
        $response = $middleware->handle($request, function () {
            return response('Authorized');
        });

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('error'), $response->getTargetUrl());
    }
}
