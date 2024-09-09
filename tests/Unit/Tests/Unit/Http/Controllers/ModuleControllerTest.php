<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\ModuleController;
use App\Http\Requests\ModuleUpdateRequest;
use App\Models\Exam;
use App\Models\Module;
use App\Models\Result;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\TeacherModule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Tests\TestCase;

class ModuleControllerTest extends TestCase
{

    use RefreshDatabase;

    public function test_index()
    {

        $userProf = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'testprof@test.fr',
            'password' => '12345678',
            'role' => 'teacher',
            'security_answer' => 'answer'
        ]);

        $teacher = Student::create([
            'user_id'=>$userProf->id,
            'active'=>1
        ]);
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
        // Créez un module fictif
        $moduleData = [
            'name' => 'Module Test',
            'coefficient' => 5,
            'user_id'=> $userProf->id
        ];


        $module=Module::create($moduleData);
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

        $exam2 = Exam::create(
            [
                'name' => 'test_2',
                'module_id' => $module->id,
                'coefficient' => 5,
            ]
        );

        Result::create([
            'student_id' => $student->id,
            'exam_id' => $exam2->id,
            'mark' => 20
        ]);
        $this->actingAs($user);
        $controller = new ModuleController();
        $response = $controller->index();
        $this->assertInstanceOf(View::class,$response);
        $this->assertEquals('modules.index',$response->name());
    }

    public function test_show()
    {
        $user = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'test@test.fr',
            'password' => '12345678',
            'role' => 'teacher',
            'security_answer' => 'answer'
        ]);
        // Créez un module fictif
        $moduleData = [
            'name' => 'Module Test',
            'coefficient' => 5,
            'user_id'=> $user->id
        ];


        $module=Module::create($moduleData);
        $this->actingAs($user);
        $controller = new ModuleController();
        $response = $controller->show($module->name);
        $this->assertInstanceOf(View::class,$response);
        $this->assertEquals('modules.show',$response->name());
    }

    public function test_show_module_not_exists()
    {
        $user = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'test@test.fr',
            'password' => '12345678',
            'role' => 'teacher',
            'security_answer' => 'answer'
        ]);


        $this->actingAs($user);
        $controller = new ModuleController();
        $response = $controller->show('MATHS');
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('module.index'), $response->getTargetUrl());
    }
    public function testUpdate()
    {
        $user = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'test@test.fr',
            'password' => '12345678',
            'role' => 'teacher',
            'security_answer' => 'answer'
        ]);
        // Créez un module fictif
        $moduleData = [
            'name' => 'Module Test',
            'coefficient' => 5,
            'user_id'=> $user->id
        ];


        $module = Module::create($moduleData);

        // Données de test pour la mise à jour
        $updateData = [
            'module_id' => $module->id,
            'name' => 'Updated Module',
            'coefficient' => 10,
            'user_id'=>$user->id
        ];

        $request = Request::create('/module', 'POST', $updateData);

        $controller = new ModuleController();
        $response = $controller->update($request);


        $this->assertDatabaseHas('modules', [
            'name' => 'Updated Module',
            'coefficient' => '10',
            'user_id'=> $user->id
        ]);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('module.index'), $response->getTargetUrl());
    }

    public function test_update_not_found()
    {
        $user = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'test@test.fr',
            'password' => '12345678',
            'role' => 'teacher',
            'security_answer' => 'answer'
        ]);


        // Données de test pour la mise à jour
        $updateData = [
            'module_id' => 50,
            'name' => 'Updated Module',
            'coefficient' => 10,
            'user_id'=>$user->id
        ];

        $request = Request::create('/module', 'POST', $updateData);

        $controller = new ModuleController();
        $response = $controller->update($request);
        $this->assertDatabaseMissing('modules', [
            'name' => 'Updated Module',
            'coefficient' => '10',
            'user_id'=> $user->id
        ]);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('module.index'), $response->getTargetUrl());
    }

    public function testChoisirModule()
    {
        $user = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'test@test.fr',
            'password' => '12345678',
            'role' => 'teacher',
            'security_answer' => 'answer'
        ]);
        // Créez un module fictif
        $moduleData = [
            'name' => 'Module Test',
            'coefficient' => 5,
            'user_id'=> $user->id
        ];


        $module = Module::create($moduleData);

        $controller = new ModuleController();
        // Appelez la route choisirModule avec l'ID du module
        $response = $this->get(route('module.choisir', ['module_id' => $module->id]));

        // Vérifiez la réponse
        $response->assertRedirect(route('module.name.show', ['nommodule' => $module->name]));
    }

    public function testShowStudents()
    {
        $user = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'test@test.fr',
            'password' => '12345678',
            'role' => 'teacher',
            'security_answer' => 'answer'
        ]);
        // Create a fake module
        $moduleData = [
            'name' => 'Module Test',
            'coefficient' => 5,
            'user_id'=> $user->id
        ];

        $module = Module::create($moduleData);
        $this->actingAs($user);
        $controller = new ModuleController();
        // Call the addStudents route with the module name
        $response = $this->get(route('module.name.add-students', ['nommodule' => $module->name]));

        // Assert that the response has a successful status code
        $response->assertStatus(200);
    }

    public function testShowTeachers()
    {
        $user2 = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'test2@test.fr',
            'password' => '12345678',
            'role' => 'teacher',
            'security_answer' => 'answer'
        ]);

        $user = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'test@test.fr',
            'password' => '12345678',
            'role' => 'teacher',
            'security_answer' => 'answer'
        ]);
        // Create a fake module
        $moduleData = [
            'name' => 'Module Test',
            'coefficient' => 5,
            'user_id'=> $user->id
        ];

        $module = Module::create($moduleData);
        $this->actingAs($user2);
        $controller = new ModuleController();
        // Call the addStudents route with the module name
        $response = $this->get(route('module.name.add-teachers', ['nommodule' => $module->name]));

        // Assert that the response has a successful status code
        $response->assertStatus(200);
    }

    public function testAssignStudents()
    {
        $userProf = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'test@testz.fr',
            'password' => '12345678',
            'role' => 'teacher',
            'security_answer' => 'answer'
        ]);
        // Create a fake module
        $moduleData = [
            'name' => 'Module Test',
            'coefficient' => 5,
            'user_id'=> $userProf->id
        ];

        $module = Module::create($moduleData);

        // Create a student
        $studentData = [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'birthdate' => '1990-01-01',
            'email' => 'john.doe@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'security_answer'=>'OUI'
        ];

        $user = User::class::create($studentData);

        $attributes2 = [
            'active' => false,
            'user_id' => $user->id,
        ];

        $student = Student::class::create($attributes2);

        // Data for assigning students
        $assignData = [
            'selected_students' => [$student->id],
        ];

        // Create a POST request with assignment data
        $request = Request::create('/module/assign', 'POST', $assignData);

        $controller = new ModuleController();
        $response = $controller->assignStudents($request, $module->name);

        // Assert that the response is a RedirectResponse and redirects to the module name show route
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('module.name.show', ['nommodule' => $module->name]), $response->getTargetUrl());
    }

    public function testAssignTeachers()
    {
        $userProf = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'test@testz.fr',
            'password' => '12345678',
            'role' => 'teacher',
            'security_answer' => 'answer'
        ]);
        // Create a fake module
        $moduleData = [
            'name' => 'Module Test',
            'coefficient' => 5,
            'user_id'=> $userProf->id
        ];

        $module = Module::create($moduleData);

        // Create a teacher
        $attributes = [
            'firstname' => 'Jofhn',
            'lastname' => 'Dofe',
            'birthdate' => '1990-01-01',
            'email' => 'jofhn.dofe@gmail.com',
            'password' => Hash::make('passzword'),
            'role' => 'teacher',
            'security_answer'=>'OUI'
        ];

        $user = User::class::create($attributes);

        $attributes2 = [
            'active' => false,
            'user_id' => $user->id,
        ];
        $teacher = Teacher::class::create($attributes2);

        // Data for assigning teachers
        $assignData = [
            'selected_teachers' => [$teacher->id],
        ];

        // Create a POST request with assignment data
        $request = Request::create('/module/assign', 'POST', $assignData);

        $controller = new ModuleController();
        $response = $controller->assignTeachers($request, $module->name);

        // Assert that the response is a RedirectResponse and redirects to the module name show route
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('module.name.show', ['nommodule' => $module->name]), $response->getTargetUrl());
    }



    public function testAddStudent()
    {
        $userProf = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'test@testz.fr',
            'password' => '12345678',
            'role' => 'teacher',
            'security_answer' => 'answer'
        ]);
        // Create a fake module
        $moduleData = [
            'name' => 'Module Test',
            'coefficient' => 5,
            'user_id'=> $userProf->id
        ];

        $module = Module::create($moduleData);
        $this->actingAs($userProf);
        $controller = new ModuleController();
        // Call the addStudent route with the module name
        $response = $this->get(route('module.name.add-students', ['nommodule' => $module->name]));

        // Assert that the response has a successful status code
        $response->assertStatus(200);
    }

    public function testAddTeacher()
    {
        $userProf = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'test@testz.fr',
            'password' => '12345678',
            'role' => 'teacher',
            'security_answer' => 'answer'
        ]);
        // Create a fake module
        $moduleData = [
            'name' => 'Module Test',
            'coefficient' => 5,
            'user_id'=> $userProf->id
        ];

        $module = Module::create($moduleData);
        $this->actingAs($userProf);
        $controller = new ModuleController();
        // Call the addTeacher route with the module name
        $response = $this->get(route('module.name.add-teachers', ['nommodule' => $module->name]));

        // Assert that the response has a successful status code
        $response->assertStatus(200);
    }

    public function test_assign_student()
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

        $student = Student::create(
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
        $this->actingAs($user);
        $response = $this->post(route('module.name.assign', ['nommodule' => $module->name]), [
            'type' => 'student',
            'selected_students' => [$student->id],
        ]);



        $response->assertRedirect(route('module.name.show', ['nommodule' => $module->name]));

        $response->assertSessionHas('status', 'Le(s) étudiant(s) ont été ajouté(s) au module');

    }

    public function test_assign_teacher()
    {
        $user = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'test@test.fr',
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

        $module = Module::create(
            [
                'name' => 'test',
                'coefficient' => 6,
                'user_id'=>$user->id
            ]
        );
        $this->actingAs($user);

        $response = $this->post(route('module.name.assign', ['nommodule' => $module->name]), [
            'type' => 'teacher',
            'selected_teachers' => [$teacher->id],
        ]);



        $response->assertRedirect(route('module.name.show', ['nommodule' => $module->name]));

        $response->assertSessionHas('status', 'Le(s) enseignant(s) ont été ajouté(s) au module');
    }

    public function test_ask_assign_teacher()
    {
        $user = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'test@test.fr',
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


        $user2 = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'test2@test.fr',
            'password' => '12345678',
            'role' => 'teacher',
            'security_answer' => 'answer'
        ]);

        $teacher2 = Teacher::create(
            [
                'user_id' => $user2->id,
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
        $this->actingAs($user2);

        $response = $this->post(route('module.name.assign', ['nommodule' => $module->name]), [
            'type' => 'ask',
        ]);



        $response->assertRedirect(route('module.name.show', ['nommodule' => $module->name]));

        $response->assertSessionHas('status', 'Demande d\'accès au module effectué.');
    }

    public function test_ask_assign_student()
    {
        $user = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'test@test.fr',
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


        $user2 = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'test2@test.fr',
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

        $module = Module::create(
            [
                'name' => 'test',
                'coefficient' => 6,
                'user_id'=>$user->id
            ]
        );
        $this->actingAs($user2);

        $response = $this->post(route('module.name.assign', ['nommodule' => $module->name]), [
            'type' => 'ask',
        ]);



        $response->assertRedirect(route('module.name.show', ['nommodule' => $module->name]));

        $response->assertSessionHas('status', 'Demande d\'accès au module effectué.');
    }

    public function test_create()
    {
        $controller = new ModuleController();
        $response=$controller->create();


        $this->assertInstanceOf(View::class,$response);
        $this->assertEquals('modules.create',$response->name());


    }

    public function test_store()
    {
        $user = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'test@test.fr',
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

        $request = $this->mock(Request::class);
        $request->shouldReceive('input')
            ->with('nom')
            ->andReturn('TF');
        $request->shouldReceive('input')
            ->with('coefficient')
            ->andReturn(2);
        $request->shouldReceive('input')
            ->with('user_id')
            ->andReturn($user->id);

        $controller = new ModuleController();
        $response=$controller->store($request);
        $this->assertDatabaseHas('modules', [
            'name' => 'TF',
            'coefficient' => 2,
            'user_id' => $user->id,
        ]);
        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
    }

    public function testDelete()
    {
        $user = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'test@test.fr',
            'password' => '12345678',
            'role' => 'teacher',
            'security_answer' => 'answer'
        ]);

        $student = Student::create(
            [
                'user_id' => $user->id,
                'active' => 1,
            ]
        );

        $module = Module::create(
            [
                'name' => 'TF',
                'coefficient' => 2,
                'user_id'=>$user->id
            ]
        );

        $controller = new ModuleController();
        $this->assertDatabaseHas('modules', [
            'name' => 'TF',
            'coefficient' => 2,
            'user_id' => $user->id,
        ]);
        $controller->delete($module->id);
        $this->assertDatabaseMissing('modules', [
            'name' => 'TF',
            'coefficient' => 2,
            'user_id' => $user->id,
        ]);
    }


    public function test_student_and_teacher_in_module_with_teacher_auth()
    {
        $user = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'test@test.fr',
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


        $user2 = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'test2@test.fr',
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
        // Créez un module fictif
        $moduleData = [
            'name' => 'Module Test',
            'coefficient' => 5,
            'user_id'=> $user->id
        ];
        $module=Module::create($moduleData);

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

        $this->actingAs($user);

        $controller = new ModuleController();
        $response = $controller->StudentAndTeacherInModule($module->name);
        $this->assertInstanceOf(View::class,$response);
        $this->assertEquals('modules.show',$response->name());
    }

    public function test_student_and_teacher_in_module_with_student_auth()
    {
        $user = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'test@test.fr',
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


        $user2 = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'test2@test.fr',
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
        // Créez un module fictif
        $moduleData = [
            'name' => 'Module Test',
            'coefficient' => 5,
            'user_id'=> $user->id
        ];
        $module=Module::create($moduleData);
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

        $this->actingAs($user2);

        $controller = new ModuleController();
        $response = $controller->StudentAndTeacherInModule($module->name);
        $this->assertInstanceOf(View::class,$response);
        $this->assertEquals('modules.show',$response->name());
    }

    public function test_show_demands()
    {
        $user = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'test@test.fr',
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



        // Créez un module fictif
        $moduleData = [
            'name' => 'Module Test',
            'coefficient' => 5,
            'user_id'=> $user->id
        ];
        $module=Module::create($moduleData);

        $this->actingAs($user);

        $controller = new ModuleController();
        $response = $controller->showDemands();
        $this->assertInstanceOf(View::class,$response);
        $this->assertEquals('demandes.liste-utilisateurs-demandes-module',$response->name());
    }

    public function test_accept_demands()
    {
        $user = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'test@test.fr',
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

        $user2 = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'test2@test.fr',
            'password' => '12345678',
            'role' => 'teacher',
            'security_answer' => 'answer'
        ]);

        $teacher2 = Teacher::create(
            [
                'user_id' => $user2->id,
                'active' => 1,
            ]
        );



        // Créez un module fictif
        $moduleData = [
            'name' => 'Module Test',
            'coefficient' => 5,
            'user_id'=> $user->id
        ];
        $module=Module::create($moduleData);

        $teacherModule = TeacherModule::create([
            'module_id'=>$module->id,
            'teacher_id'=>$teacher2->id,
            'active'=>0
        ]);

        $this->actingAs($user);

        $controller = new ModuleController();
        $response = $controller->acceptDemand($teacherModule->id,$user2->role);
        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $this->assertDatabaseHas('teacher_modules', [
            'module_id' => $module->id,
            'teacher_id'=>$teacher2->id,
            'active'=>1
        ]);
    }

    public function test_reject_demands()
    {
        $user = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'test@test.fr',
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

        $user2 = User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'birthdate' => '1111-11-11',
            'email' => 'test2@test.fr',
            'password' => '12345678',
            'role' => 'teacher',
            'security_answer' => 'answer'
        ]);

        $teacher2 = Teacher::create(
            [
                'user_id' => $user2->id,
                'active' => 1,
            ]
        );



        // Créez un module fictif
        $moduleData = [
            'name' => 'Module Test',
            'coefficient' => 5,
            'user_id'=> $user->id
        ];
        $module=Module::create($moduleData);

        $teacherModule = TeacherModule::create([
            'module_id'=>$module->id,
            'teacher_id'=>$teacher2->id,
            'active'=>0
        ]);

        $this->actingAs($user);

        $controller = new ModuleController();
        $response = $controller->rejectDemand($teacherModule->id,$user2->role);
        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $this->assertDatabaseMissing('teacher_modules', [
            'module_id' => $module->id,
            'teacher_id'=>$teacher2->id,
            'active'=>1
        ]);
        $this->assertDatabaseMissing('teacher_modules', [
            'module_id' => $module->id,
            'teacher_id'=>$teacher2->id,
            'active'=>0
        ]);
    }
}

