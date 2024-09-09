<?php

namespace Tests\Unit\Tests\Unit\Http\Controllers;

use App\Http\Controllers\Admin\CreateAdminController;
use App\Http\Controllers\HistogramController;
use App\Models\Exam;
use App\Models\Module;
use App\Models\Result;
use App\Models\Student;
use App\Models\StudentModule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Tests\TestCase;
use Tests\Unit\Tests\Unit\Http\Controllers\Admin\AllUserControllerTest;

class HistogramControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_histogram()
    {
        $controller = new HistogramController();
        $response = $controller->showHistogram();
        $this->assertInstanceOf(View::class,$response);
        $this->assertEquals('histogram',$response->name());
    }

    public function test_show_student_average()
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

        $student = Student::create([
            'user_id'=>$user->id,
            'active'=>1
        ]);

        $this->module = Module::create(
            [
                'name' => 'test',
                'coefficient' => 6,
                'user_id'=>$user->id
            ]
        );

        StudentModule::create(
            [
                'student_id' => $student->id,
                'module_id' => $this->module->id
            ]
        );

        $this->exam = Exam::create(
            [
                'name' => 'test_1',
                'module_id' => $this->module->id,
                'coefficient' => 3,
            ]
        );

        Result::create([
            'student_id' => $student->id,
            'exam_id' => $this->exam->id,
            'mark' => 0
        ]);
        $this->actingAs($user);
        $controller = new HistogramController();
        $response = $controller->showStudentAverages();
        $this->assertInstanceOf(View::class,$response);
        $this->assertEquals('student_averages',$response->name());
    }
}
