<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\ExamController;
use App\Models\Exam;
use App\Models\Module;
use App\Models\Result;
use App\Models\Student;
use App\Models\StudentModule;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class ExamControllerTest extends TestCase
{
    use RefreshDatabase;

    private Module $module;
    private Student $student;
    private Exam $exam;
    protected function setUp(): void
    {
        parent::setUp();

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

        $this->module = Module::create(
            [
                'name' => 'test',
                'coefficient' => 6,
                'user_id'=>$user->id
            ]
        );

        StudentModule::create(
            [
                'student_id' => $this->student->id,
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
            'student_id' => $this->student->id,
            'exam_id' => $this->exam->id,
            'mark' => 0
        ]);
    }

    public function test_index_existing_module() : void
    {
        $controller = new ExamController();
        $response = $controller->index($this->module->name);
        $this->assertInstanceOf(View::class, $response);
        $this->assertEquals('exams.index', $response->getName());
        $this->assertEquals('test', $response->getData()['module_name']);
    }

    public function test_index_non_existing_module() : void
    {
        $this->expectException(NotFoundHttpException::class);

        $examController = new ExamController();
        $examController->index('yo');
    }

    public function test_show_existing_module() : void
    {
        $controller = new ExamController();
        $response = $controller->show($this->module->name, $this->exam->id);
        $this->assertInstanceOf(View::class, $response);
        $this->assertEquals('exams.show', $response->getName());
        $this->assertEquals($this->module->name, $response->getData()['module_name']);
        $this->assertEquals($this->exam->id, $response->getData()['exam_id']);
    }

    public function test_show_non_existing_module() : void
    {
        $this->expectException(NotFoundHttpException::class);

        $examController = new ExamController();
        $examController->show('yo', $this->exam->id);
    }

    public function test_create_exam_correct_view() : void
    {
        $controller = new ExamController();
        $response = $controller->create($this->module->name);
        $this->assertInstanceOf(View::class, $response);
        $this->assertEquals('exams.create-exam', $response->getName());
        $this->assertEquals('test', $response->getData()['nommodule']);
    }

    public function test_create_exam_non_existing_module() : void
    {
        $this->expectException(NotFoundHttpException::class);

        $examController = new ExamController();
        $examController->create('yo');
    }

    public function test_create_exam() : void
    {
        $request = $this->mock(Request::class);
        $request->shouldReceive('input')
            ->with('exam-name')
            ->andReturn('test_2');
        $request->shouldReceive('input')
            ->with('coefficient')
            ->andReturn(42);

        $examController = new ExamController();
        $examController->store($request, $this->module->name);

         $this->assertDatabaseHas('exams',
             [
                 'module_id' => $this->module->id,
                 'name' => 'test_2',
                 'coefficient' => 42,
             ]
         );
    }

    public function test_create_exam_negative_coefficient() : void
    {
        $request = $this->mock(Request::class);
        $request->shouldReceive('input')
            ->with('exam-name')
            ->andReturn('test_2');
        $request->shouldReceive('input')
            ->with('coefficient')
            ->andReturn(-1);

        $examController = new ExamController();
        $examController->store($request, $this->module->name);

        $this->assertDatabaseMissing('exams',
            [
                'module_id' => $this->module->id,
                'name' => 'test_2',
                'coefficient' => -1,
            ]
        );
    }

    public function test_create_exam_empty_name() : void
    {
        $request = $this->mock(Request::class);
        $request->shouldReceive('input')
            ->with('exam-name')
            ->andReturn('');
        $request->shouldReceive('input')
            ->with('coefficient')
            ->andReturn(10);

        $examController = new ExamController();
        $examController->store($request, $this->module->name);

        $this->assertDatabaseMissing('exams',
            [
                'module_id' => $this->module->id,
                'name' => '',
                'coefficient' => 10,
            ]
        );
    }

    public function test_create_exam_null_name() : void
    {
        $request = $this->mock(Request::class);
        $request->shouldReceive('input')
            ->with('exam-name')
            ->andReturn(null);
        $request->shouldReceive('input')
            ->with('coefficient')
            ->andReturn(10);

        $examController = new ExamController();
        $examController->store($request, $this->module->name);

        $this->assertDatabaseMissing('exams',
            [
                'module_id' => $this->module->id,
                'name' => null,
                'coefficient' => 10,
            ]
        );
    }

    public function test_create_exam_empty_coefficient() : void
    {
        $request = $this->mock(Request::class);
        $request->shouldReceive('input')
            ->with('exam-name')
            ->andReturn('test_2');
        $request->shouldReceive('input')
            ->with('coefficient')
            ->andReturn(null);

        $examController = new ExamController();
        $examController->store($request, $this->module->name);

        $this->assertDatabaseMissing('exams',
            [
                'module_id' => $this->module->id,
                'name' => 'test_2',
                'coefficient' => null,
            ]
        );
    }

    public function test_create_already_existing_exam() : void
    {
        $request = $this->mock(Request::class);
        $request->shouldReceive('input')
            ->with('exam-name')
            ->andReturn('test_2');
        $request->shouldReceive('input')
            ->with('coefficient')
            ->andReturn(10);

        $request_2 = $this->mock(Request::class);
        $request_2->shouldReceive('input')
            ->with('exam-name')
            ->andReturn('test_2');
        $request_2->shouldReceive('input')
            ->with('coefficient')
            ->andReturn(5);

        $examController = new ExamController();

        $examController->store($request, $this->module->name);
        $examController->store($request_2, $this->module->name);

        $this->assertDatabaseHas('exams',
            [
                'module_id' => $this->module->id,
                'name' => 'test_2',
                'coefficient' => 10,
            ]
        );

        $this->assertDatabaseMissing('exams',
            [
                'module_id' => $this->module->id,
                'name' => 'test_2',
                'coefficient' => 5,
            ]
        );
    }

    public function test_add_correct_mark() : void
    {
        $request = $this->mock(Request::class);
        $request->shouldReceive('input')
            ->with('mark')
            ->andReturn(15);


        $examController = new ExamController();
        $examController->updateExamMark($request, 'test', $this->exam->id, $this->student->user_id);

        $this->assertDatabaseHas('results',
            [
                'student_id' => $this->student->id,
                'exam_id' => $this->exam->id,
                'mark' => 15
            ]
        );
    }

    public function test_add_mark_min_inside_range() : void
    {
        $request = $this->mock(Request::class);
        $request->shouldReceive('input')
            ->with('mark')
            ->andReturn(0);

        $examController = new ExamController();
        $examController->updateExamMark($request, 'test', $this->exam->id, $this->student->user_id);


        $this->assertDatabaseHas('results',
            [
                'student_id' => $this->student->id,
                'exam_id' => $this->exam->id,
                'mark' => 0
            ]
        );
    }

    public function test_add_mark_max_inside_range() : void
    {
        $request = $this->mock(Request::class);
        $request->shouldReceive('input')
            ->with('mark')
            ->andReturn(20);

        $examController = new ExamController();
        $examController->updateExamMark($request, 'test', $this->exam->id, $this->student->user_id);


        $this->assertDatabaseHas('results',
            [
                'student_id' => $this->student->id,
                'exam_id' => $this->exam->id,
                'mark' => 20
            ]
        );
    }

    public function test_add_mark_outside_range() : void
    {
        $request = $this->mock(Request::class);
        $request->shouldReceive('input')
            ->with('mark')
            ->andReturn(21);

        $examController = new ExamController();
        $examController->updateExamMark($request, 'test', $this->exam->id, $this->student->user_id);

        $this->assertDatabaseMissing('results',
            [
                'student_id' => $this->student->id,
                'exam_id' => $this->exam->id,
                'mark' => 21
            ]
        );
    }

    public function test_add_negative_mark() : void
    {
        $request = $this->mock(Request::class);
        $request->shouldReceive('input')
            ->with('mark')
            ->andReturn(-1);

        $examController = new ExamController();
        $examController->updateExamMark($request, 'test', $this->exam->id, $this->student->user_id);

        $this->assertDatabaseMissing('results',
            [
                'student_id' => $this->student->id,
                'exam_id' => $this->exam->id,
                'mark' => -1
            ]
        );
    }

    public function test_delete_existing_mark() : void
    {
        $request = $this->mock(Request::class);
        $request->shouldReceive('input')
            ->with('mark')
            ->andReturn(null);

        $examController = new ExamController();
        $examController->updateExamMark($request, 'test', $this->exam->id, $this->student->user_id);

        $this->assertDatabaseHas('results',
            [
                'student_id' => $this->student->id,
                'exam_id' => $this->exam->id,
                'mark' => null
            ]
        );
    }

    public function test_modify_existing_mark() : void
    {
        $request = $this->mock(Request::class);
        $request->shouldReceive('input')
            ->with('mark')
            ->andReturn(15);
        $request_2 = $this->mock(Request::class);
        $request_2->shouldReceive('input')
            ->with('mark')
            ->andReturn(16);

        $examController = new ExamController();
        $examController->updateExamMark($request, 'test', $this->exam->id, $this->student->user_id);

        $this->assertDatabaseHas('results',
            [
                'student_id' => $this->student->id,
                'exam_id' => $this->exam->id,
                'mark' => 15
            ]
        );

        $examController->updateExamMark($request_2, 'test', $this->exam->id, $this->student->user_id);

        $this->assertDatabaseHas('results',
            [
                'student_id' => $this->student->id,
                'exam_id' => $this->exam->id,
                'mark' => 16
            ]
        );
    }

    public function test_modify_existing_mark_outside_range() : void
    {
        $request = $this->mock(Request::class);
        $request->shouldReceive('input')
            ->with('mark')
            ->andReturn(15);
        $request_2 = $this->mock(Request::class);
        $request_2->shouldReceive('input')
            ->with('mark')
            ->andReturn(21);

        $examController = new ExamController();
        $examController->updateExamMark($request, 'test', $this->exam->id, $this->student->user_id);

        $this->assertDatabaseHas('results',
            [
                'student_id' => $this->student->id,
                'exam_id' => $this->exam->id,
                'mark' => 15
            ]
        );

        $examController->updateExamMark($request_2, 'test', $this->exam->id, $this->student->user_id);

        $this->assertDatabaseHas('results',
            [
                'student_id' => $this->student->id,
                'exam_id' => $this->exam->id,
                'mark' => 15
            ]
        );
    }

    public function test_modify_existing_mark_negative() : void
    {
        $request = $this->mock(Request::class);
        $request->shouldReceive('input')
            ->with('mark')
            ->andReturn(15);
        $request_2 = $this->mock(Request::class);
        $request_2->shouldReceive('input')
            ->with('mark')
            ->andReturn(-42);

        $examController = new ExamController();
        $examController->updateExamMark($request, 'test', $this->exam->id, $this->student->user_id);

        $this->assertDatabaseHas('results',
            [
                'student_id' => $this->student->id,
                'exam_id' => $this->exam->id,
                'mark' => 15
            ]
        );

        $examController->updateExamMark($request_2, 'test', $this->exam->id, $this->student->user_id);

        $this->assertDatabaseHas('results',
            [
                'student_id' => $this->student->id,
                'exam_id' => $this->exam->id,
                'mark' => 15
            ]
        );
    }

    public function test_delete_existing_exam_no_student() : void
    {
        $exam = Exam::create(
            [
                'name' => 'test_2',
                'module_id' => $this->module->id,
                'coefficient' => 42,
            ]
        );

        $this->assertDatabaseHas('exams',
            [
                'name' => 'test_2',
                'module_id' => $this->module->id,
                'coefficient' => 42,
            ]
        );

        $examController = new ExamController();
        $response = $examController->deleteExam($this->module->name, $exam->id);

        $this->assertInstanceOf(JsonResponse::class, $response);

        $this->assertJson($response->getContent());
        $data = json_decode($response->getContent(), true);

        $this->assertEquals(['message' => 'Examen supprimé avec succès'], $data);

        $this->assertDatabaseMissing('exams',
            [
                'name' => 'test_2',
                'module_id' => $this->module->id,
                'coefficient' => 42,
            ]
        );
    }

    public function test_delete_existing_exam_single_student() : void
    {
        $exam = Exam::create(
            [
                'name' => 'test_2',
                'module_id' => $this->module->id,
                'coefficient' => 42,
            ]
        );

         Result::create([
            'student_id' => $this->student->id,
            'exam_id' => $exam->id,
            'mark' => 0
        ]);

        $this->assertDatabaseHas('exams',
            [
                'name' => 'test_2',
                'module_id' => $this->module->id,
                'coefficient' => 42,
            ]
        );

        $this->assertDatabaseHas('results',
            [
                'student_id' => $this->student->id,
                'exam_id' => $exam->id,
                'mark' => 0
            ]
        );

        $examController = new ExamController();
        $response = $examController->deleteExam($this->module->name, $exam->id);

        $this->assertInstanceOf(JsonResponse::class, $response);

        $this->assertJson($response->getContent());
        $data = json_decode($response->getContent(), true);

        $this->assertEquals(['message' => 'Examen supprimé avec succès'], $data);

        $this->assertDatabaseMissing('exams',
            [
                'name' => 'test_2',
                'module_id' => $this->module->id,
                'coefficient' => 42,
            ]
        );

        $this->assertDatabaseMissing('results',
            [
                'student_id' => $this->student->id,
                'exam_id' => $exam->id,
                'mark' => 0
            ]
        );
    }

    public function test_delete_existing_exam_many_student() : void {
        $user = User::create(
            [
                'firstname' => 'test2',
                'lastname' => 'test2',
                'birthdate' => '1111-11-11',
                'email' => 'test@test.com',
                'password' => 12345678,
                'role' => 'student',
                'security_answer' => 'answer'
            ]
        );

        $student = Student::create(
            [
                'user_id' => $user->id,
                'active' => 1,
            ]
        );

        $exam = Exam::create(
            [
                'name' => 'test_2',
                'module_id' => $this->module->id,
                'coefficient' => 42,
            ]
        );

        Result::create([
            'student_id' => $this->student->id,
            'exam_id' => $exam->id,
            'mark' => 0
        ]);

        Result::create([
            'student_id' => $student->id,
            'exam_id' => $exam->id,
            'mark' => 10
        ]);

        $this->assertDatabaseHas('students',
            [
                'user_id' => $user->id,
                'active' => 1,
            ]
        );

        $this->assertDatabaseHas('exams',
            [
                'name' => 'test_2',
                'module_id' => $this->module->id,
                'coefficient' => 42,
            ]
        );

        $this->assertDatabaseHas('results',
            [
                'student_id' => $this->student->id,
                'exam_id' => $exam->id,
                'mark' => 0
            ]
        );

        $this->assertDatabaseHas('results',
            [
                'student_id' => $student->id,
                'exam_id' => $exam->id,
                'mark' => 10
            ]
        );

        $examController = new ExamController();
        $response = $examController->deleteExam($this->module->name, $exam->id);

        $this->assertInstanceOf(JsonResponse::class, $response);

        $this->assertJson($response->getContent());
        $data = json_decode($response->getContent(), true);

        $this->assertEquals(['message' => 'Examen supprimé avec succès'], $data);

        $this->assertDatabaseHas('students',
            [
                'user_id' => $user->id,
                'active' => 1,
            ]
        );

        $this->assertDatabaseMissing('exams',
            [
                'name' => 'test_2',
                'module_id' => $this->module->id,
                'coefficient' => 42,
            ]
        );

        $this->assertDatabaseMissing('results',
            [
                'student_id' => $this->student->id,
                'exam_id' => $exam->id,
                'mark' => 0
            ]
        );

        $this->assertDatabaseMissing('results',
            [
                'student_id' => $student->id,
                'exam_id' => $exam->id,
                'mark' => 10
            ]
        );
    }

    public function test_delete_non_existing_exam() : void
    {
        $examController = new ExamController();
        $response = $examController->deleteExam($this->module->name, 42);

        $this->assertInstanceOf(JsonResponse::class, $response);

        $this->assertJson($response->getContent());
        $data = json_decode($response->getContent(), true);

        $this->assertEquals(['message' => 'Erreur lors de la suppression de l\'examen'], $data);
    }
}
