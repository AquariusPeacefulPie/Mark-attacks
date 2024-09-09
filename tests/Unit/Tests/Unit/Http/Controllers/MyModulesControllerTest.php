<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\MyModulesController;
use App\Models\Admin;
use App\Models\Exam;
use App\Models\Result;
use App\Models\Student;
use App\Models\StudentModule;
use App\Models\TeacherModule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Tests\TestCase;

use App\Models\Module;
use App\Models\Teacher;
use App\Http\Controllers\ModuleController;


class MyModulesControllerTest extends TestCase
{

    use RefreshDatabase;
    public function testViewAsStudent()
    {

        $user = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'testprof@test.fr',
            'password' => '12345678',
            'role' => 'teacher',
            'security_answer' => 'answer'
        ]);

        $teacher = Teacher::create(
            [
                'user_id' => $user->id,
                'active' => 1,
            ]
        );

        $moduleName = "Module Test";

        // Créez un module fictif
        $moduleData = [
            'name' => $moduleName,
            'coefficient' => 5,
            'user_id' => $user->id
        ];

        $module = Module::create($moduleData);

        $student = $this->addStudentToDB('test@mail.fr','2000-01-01','Test','Test','1','answer');

        $studentData = [
            'selected_students' => [$student->id]
        ];

        // create request to assign students to newly created module
        $request = Request::create("/module/".$moduleName."/assign-students",'POST',$studentData);

        $moduleController = new ModuleController();
        // send created request
        $response = $moduleController->assignStudents($request,$moduleName);
        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);


    }

    public function testViewAsTeacher() {
        $user = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'testprof@test.fr',
            'password' => '12345678',
            'role' => 'teacher',
            'security_answer' => 'answer'
        ]);

        $teacher = Teacher::create(
            [
                'user_id' => $user->id,
                'active' => 1,
            ]
        );

        $moduleName = "Module Test";

        // Créez un module fictif
        $moduleData = [
            'name' => $moduleName,
            'coefficient' => 5,
            'user_id' => $user->id
        ];

        $module = Module::create($moduleData);

        $teacher2 = $this->addTeacherToDB('test@mail.fr','2000-01-01','Test','Test','1','answer');

        $teacherData = [
            'selected_students' => [$teacher2->id]
        ];

        // create request to assign students to newly created module
        $request = Request::create("/module/".$moduleName."/assign-teachers",'POST',$teacherData);

        $moduleController = new ModuleController();
        // send created request
        $response = $moduleController->assignTeachers($request,$moduleName);
        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
    }

    public function test_show_my_modules_with_teacher() {

        $user = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'test@test.fr',
            'password' => '12345678',
            'role' => 'student',
            'security_answer' => 'answer'
        ]);

        $this->student = Student::create(
            [
                'user_id' => $user->id,
                'active' => 1,
            ]
        );

        $module = Module::create(
            [
                'name' => 'test',
                'coefficient' => 6,
                'user_id'=>$user->id
            ]
        );



        $user2 = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'testprof1@test.fr',
            'password' => '12345678',
            'role' => 'teacher',
            'security_answer' => 'answer'
        ]);

        $teacher = Teacher::create(
            [
                'user_id' => $user2->id,
                'active' => 1,
            ]
        );

        TeacherModule::create(
            [
                'teacher_id' => $teacher->id,
                'module_id' => $module->id
            ]
        );

        $exam = Exam::create(
            [
                'name' => 'test_1',
                'module_id' => $module->id,
                'coefficient' => 3,
            ]
        );

        $request = $this->mock(Request::class);
        $request->shouldReceive('user')
            ->andReturn($user2);

        $controller = new MyModulesController();
        $response = $controller->showMyModules($request);
        $this->assertInstanceOf(View::class,$response);
        $this->assertEquals('modules.my_modules',$response->name());
    }

    public function test_show_my_modules_with_student() {

        $user = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'test@test.fr',
            'password' => '12345678',
            'role' => 'student',
            'security_answer' => 'answer'
        ]);

        $this->student = Student::create(
            [
                'user_id' => $user->id,
                'active' => 1,
            ]
        );

        $module = Module::create(
            [
                'name' => 'test',
                'coefficient' => 6,
                'user_id'=>$user->id
            ]
        );



        $user2 = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'testprof1@test.fr',
            'password' => '12345678',
            'role' => 'student',
            'security_answer' => 'answer'
        ]);

        $student = Student::create(
            [
                'user_id' => $user2->id,
                'active' => 1,
            ]
        );


        StudentModule::create(
            [
                'student_id' => $student->id,
                'module_id' => $module->id
            ]
        );

        $exam = Exam::create(
            [
                'name' => 'test_1',
                'module_id' => $module->id,
                'coefficient' => 3,
            ]
        );

        Result::create([
            'student_id' => $student->id,
            'exam_id' => $exam->id,
            'mark' => 0
        ]);
        $request = $this->mock(Request::class);
        $request->shouldReceive('user')
            ->andReturn($user2);

        $controller = new MyModulesController();
        $response = $controller->showMyModules($request);
        $this->assertInstanceOf(View::class,$response);
        $this->assertEquals('modules.my_modules',$response->name());
    }

    public function test_show_my_modules_with_admin() {

        $user = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'test@test.fr',
            'password' => '12345678',
            'role' => 'student',
            'security_answer' => 'answer'
        ]);

        $this->student = Student::create(
            [
                'user_id' => $user->id,
                'active' => 1,
            ]
        );

        $module = Module::create(
            [
                'name' => 'test',
                'coefficient' => 6,
                'user_id'=>$user->id
            ]
        );



        $user2 = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'testprof1@test.fr',
            'password' => '12345678',
            'role' => 'admin',
            'security_answer' => 'answer'
        ]);

        $admin = Admin::create(
            [
                'user_id' => $user2->id,
            ]
        );


        $request = $this->mock(Request::class);
        $request->shouldReceive('user')
            ->andReturn($user2);

        $controller = new MyModulesController();
        $response = $controller->showMyModules($request);
        $this->assertInstanceOf(View::class,$response);
        $this->assertEquals('modules.my_modules',$response->name());
    }
}
