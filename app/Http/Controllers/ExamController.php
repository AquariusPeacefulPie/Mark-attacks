<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Module;
use App\Models\Result;
use http\Env\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use PhpParser\Node\Expr\AssignOp\Mod;

class ExamController extends Controller
{
    public function index(String $moduleName) : View
    {
        $module = Module::where('name', $moduleName)
            ->first();

        if (!$module) {
            abort(404);
        }

        $exams = DB::table('exams')
            ->join('modules as m', 'm.id', '=', 'exams.module_id')
            ->where('m.name', '=', $moduleName)
            ->select([
                'exams.id',
                'exams.name',
                'exams.coefficient'
            ])
            ->distinct()
            ->get();

        return view('exams.index', compact('exams'))->with('module_name',$moduleName);
    }

    public function show(String $moduleName, String $examID) : View
    {
        $module = Module::where('name', $moduleName)
            ->first();

        if (!$module) {
            abort(404);
        }

        $examinees = DB::table('students')
            ->join('results as r', 'r.student_id', '=', 'students.id')
            ->join('exams as e', 'e.id', '=', 'r.exam_id')
            ->join('modules as m', 'm.id', '=', 'e.module_id')
            ->join('users as u', 'u.id', '=', 'students.user_id')
            ->where('m.name', '=', $moduleName)
            ->where('e.id', '=', $examID)
            ->select('u.id','u.firstname', 'u.lastname', 'r.mark')
            ->distinct()
            ->get();

        return view('exams.show', compact('examinees'))->with(['module_name' => $moduleName, 'exam_id' => $examID]);
    }

    public function create($moduleName) : View
    {
        $module = Module::where('name', $moduleName)
            ->first();

        if (!$module) {
            abort(404);
        }
        return view('exams.create-exam', ['nommodule' => $moduleName]);
    }

    public function store(Request $request, string $moduleName) : RedirectResponse
    {
        $module = Module::where('name', $moduleName)
            ->first();

        // Test exam name redundancy
        $exam = Exam::where('name', $request->input('exam-name'))
            ->first();

        if (!$exam && $request->input('coefficient') > 0 && strlen($request->input('exam-name')) > 0) {
            $exam = new Exam(
                [
                    'name' => $request->input('exam-name'),
                    'module_id' => $module->id,
                    'coefficient' => $request->input('coefficient')
                ]
            );
            $exam->save();

            // Add all students of the module to exam
            /*
            $students = DB::table('students')
                ->join('student_modules as sm', 'sm.student_id', '=', 'student_id')
                ->where('sm.module_id', '=', $module->id)->distinct()
                ->get();
            */

            $students = DB::table('student_modules as sm')
                ->where('sm.module_id', '=', $module->id)
                ->get();

            foreach($students as $student) {
                $result = new Result(
                    [
                        'student_id' => $student->student_id,
                        'exam_id' => $exam->id,
                        'mark' => 0
                    ]
                );
                $result->save();
            }

            return Redirect::route('module.name.exams.index', ['nommodule' => $moduleName])->with('success', 'Nouvel examen créé !');

        }

        return Redirect::route('module.name.exams.create', ['nommodule' => $moduleName]);
    }

    public function deleteExam($moduleName, $examId) : JsonResponse
    {
        $exam = Exam::find($examId);

        if ($exam) {
            if ($exam->delete()) {
                return response()->json(['message' => 'Examen supprimé avec succès']);
            }
        }
        return response()->json(['message' => 'Erreur lors de la suppression de l\'examen']);
    }

    public function updateExamMark(Request $request, $moduleName, $examId, $examineeId) : JsonResponse
    {
        if ($request->input('mark') >= 0 && $request->input('mark') <= 20) {
            DB::table('results')
                ->join('students as s', 's.id', '=', 'results.student_id')
                ->join('student_modules as sm', 'sm.student_id', '=', 's.id')
                ->join('modules as m', 'm.id', '=', 'sm.module_id')
                ->where('results.exam_id', '=', (int) $examId)
                ->where('s.user_id', '=', (int) $examineeId)
                ->where('m.name', '=', $moduleName)
                ->update(
                    [
                        'mark' => $request->input('mark'),
                        'results.updated_at' => new \DateTime(),
                    ]
                );

            return response()->json(['message' => 'Note mise à jour avec succès']);
        }
        return response()->json(['message' => 'Echec lors de la mise à jour']);
    }
}
