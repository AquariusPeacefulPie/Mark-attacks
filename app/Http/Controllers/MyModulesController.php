<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\Student;
use App\Models\User;
use App\Models\Module;

use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MyModulesController extends Controller
{
    public function showMyModules(Request $request) : View {
        $id = $request->user()->id;

        $currentUser = User::findOrFail($id);

        $modules = [];
        $exams = collect([]);

        switch ($currentUser->role) {
            case 'student': {
                $student = DB::table('students')
                                ->where('user_id',$id)
                                ->get()[0];

                $modules = DB::table('students')
                                    ->join('student_modules','students.id','=','student_modules.student_id')
                                    ->join('modules','student_modules.module_id','=','modules.id')
                                    ->select('modules.*')
                                    ->where('students.id',$student->id)
                                    ->get();

                foreach ($modules as $module) {
                    $module->average_mark = DB::table('results')
                            ->join('exams', 'exams.id', '=', 'results.exam_id')
                            ->join('modules', 'modules.id', '=', 'exams.module_id')
                            ->where('modules.id', $module->id)
                            ->avg('results.mark');

                    // if empty, set the average mark to '---', else format it to 2 decimals
                    $module->average_mark = $module->average_mark ? number_format($module->average_mark, 2) : '---';

                    $module->my_average_mark = DB::table('results')
                                                ->join('exams', 'exams.id', '=', 'results.exam_id')
                                                ->join('students', 'students.id', '=', 'results.student_id')
                                                ->join('modules', 'modules.id', '=', 'exams.module_id')
                                                ->where('students.id', $student->id)
                                                ->where('modules.id', $module->id)
                                                ->avg('results.mark');

                    // if empty, set the average mark to '---', else format it to 2 decimals
                    $module->my_average_mark = $module->my_average_mark ? number_format($module->my_average_mark, 2) : '---';

                    $thisModulesExams = DB::table('exams')
                                        ->select('exams.*')
                                        ->where('exams.module_id',$module->id)
                                        ->get();

                    for ($iExam = 0; $iExam < count($thisModulesExams); $iExam++){
                        $myResult = DB::table('results')
                                    ->select('results.*')
                                    ->where('results.student_id',$student->id)
                                    ->where('exam_id',$thisModulesExams[$iExam]->id)
                                    ->get()[0];

                        $thisModulesExams[$iExam]->result = $myResult->mark;

                        $average_result = DB::table('results')
                                          ->join('exams', 'exams.id', '=', 'results.exam_id')
                                          ->join('modules', 'modules.id', '=', 'exams.module_id')
                                          ->where('exams.id', $thisModulesExams[$iExam]->id)
                                          ->where('modules.id', $module->id)
                                          ->avg('results.mark');

                        // if empty, set the average mark to '---', else format it to 2 decimals
                        $average_result = $average_result ? number_format($average_result, 2) : '---';

                        $thisModulesExams[$iExam]->average_result = $average_result;
                    }

                    $exams = $exams->merge($thisModulesExams);
                }
                break;
            }

            case 'teacher': {
                $teacher = DB::table('teachers')
                            ->where('user_id',$id)
                            ->get()[0];

                $modules = DB::table('teachers')
                                ->join('teacher_modules','teachers.id','=','teacher_modules.teacher_id')
                                ->join('modules','teacher_modules.module_id','=','modules.id')
                                ->select('modules.*')
                                ->where('teachers.id',$teacher->id)
                                ->get();

                foreach ($modules as $module) {
                    $module->average_mark = DB::table('results')
                            ->join('exams', 'exams.id', '=', 'results.exam_id')
                            ->join('modules', 'modules.id', '=', 'exams.module_id')
                            ->where('modules.id', $module->id)
                            ->avg('results.mark');

                    // if empty, set the average mark to '---', else format it to 2 decimals
                    $module->average_mark = $module->average_mark ? number_format($module->average_mark, 2) : '---';

                    $thisModulesExams = DB::table('exams')
                                        ->select('exams.*')
                                        ->where('exams.module_id',$module->id)
                                        ->get();

                    for ($iExam = 0; $iExam < count($thisModulesExams); $iExam++){
                        $average_result = DB::table('results')
                                          ->join('exams', 'exams.id', '=', 'results.exam_id')
                                          ->join('modules', 'modules.id', '=', 'exams.module_id')
                                          ->where('exams.id', $thisModulesExams[$iExam]->id)
                                          ->where('modules.id', $module->id)
                                          ->avg('results.mark');

                        // if empty, set the average mark to '---', else format it to 2 decimals
                        $average_result = $average_result ? number_format($average_result, 2) : '---';

                        $thisModulesExams[$iExam]->average_result = $average_result;
                    }

                    $exams = $exams->merge($thisModulesExams);
                }
                break;
            }

            default: {
                break;
            }
        }

        return view('modules.my_modules', compact('modules','exams'));
    }
}
