<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\HistogramController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\Admin\AllUsersController;
use App\Http\Controllers\Admin\CreateAdminController;
use App\Http\Controllers\MyModulesController;
use App\Http\Controllers\Auth\ForgotPasswordController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () { //all
    if (Auth::check()) {
        return app(AllUsersController::class)->showDashboard();
    } else {
        return view('auth/login');
    }
});

Route::get('/error', function () { //all
    return view('error');
})->name('error');

/*
Route::get('/dashboard', function () {//all
    return view('dashboard');
})->middleware(['auth', 'verified','teacher'])->name('dashboard');

Route::get('/dashboard', function () {//all
    return view('dashboard');
})->middleware(['auth', 'verified','student'])->name('dashboard');

Route::get('/dashboard', [AllUsersController::class, 'getInactiveUsers'])
    ->middleware(['auth', 'verified','admin'])->name('dashboard');
*/

Route::get('/dashboard', [AllUsersController::class, 'showDashboard'])
    ->middleware(['auth', 'verified'])->name('dashboard');



Route::middleware(['auth', 'admin'])->group(function () {


        // Routes pour la liste des utilisateurs
        Route::get('/liste-utilisateurs/{type?}', [AllUsersController::class, 'listeUtilisateurs'])
            ->name('liste-utilisateurs');

        // Activation du compte
        Route::get('/liste-utilisateurs/{type}/activate/{id}', [AllUsersController::class, 'activateAccount'])
            ->name('liste-utilisateurs.activate');

        // Suppression de l'utilisateur
        Route::get('/liste-utilisateurs/{type}/delete-utilisateur/{id}', [AllUsersController::class, 'destroy'])
            ->name('liste-utilisateurs.delete');

        // Affichage du formulaire d'édition
        Route::get('/liste-utilisateurs/{type}/edit-admin/{id}', [AllUsersController::class, 'editAdmin'])
            ->name('liste-utilisateurs.edit');

        // Mise à jour de l'utilisateur
        Route::put('/liste-utilisateurs/{type}/update/{id}', [AllUsersController::class, 'update'])
            ->name('liste-utilisateurs.update');
});

Route::middleware('auth')->group(function () {
    Route::middleware('admin')->group(function () {
        Route::get('/create-admin/{category}', [CreateAdminController::class, 'create'])->name('admin.create');//admin
        Route::post('/create-admin/{category}', [CreateAdminController::class, 'store'])->name('admin.store');//admin
    });

});


Route::get('/password/reset-intermediate', [ForgotPasswordController::class, 'showResetIntermediateForm'])->name('password.reset.intermediate');//all
Route::post('/password/check-secret', [ForgotPasswordController::class, 'checkSecret'])->name('password.check-secret');//all
Route::post('/password/reset-final', [ForgotPasswordController::class, 'resetFinal'])->name('password.reset-final');//all


Route::middleware('auth')->group(function () { //all
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function () {
    Route::prefix('/module')->name('module.')->controller(ModuleController::class)->group(function () {//all
        Route::get('/', 'index')->middleware(['auth', 'verified'])->name('index');//all

        Route::middleware('admin')->group(function () { //all

            Route::get('/creer', 'create')->middleware('admin')->name('create');//admin
            Route::post('/creer', 'store')->middleware('admin')->name('store');//admin
        });
        Route::prefix('/{nommodule}')->name('name.')->group(function () {//all
            Route::get('/add-teachers', 'showTeachers')->middleware('teacher_or_admin')->name('add-teachers');//admin
            Route::get('/add-students', 'showStudents')->name('add-students');//admin et teacher
            Route::post('/', 'assign')->name('assign');//all
            Route::get('/', 'StudentAndTeacherInModule')->name('show');//all
            Route::get('/exams', [ExamController::class, 'index'])->name('exams.index');//all
            Route::get('/exam/{exam_id}', [ExamController::class, 'show'])->name('exam.show');//all
            Route::middleware('teacher_or_admin')->group(function () { //all
                Route::get('/exams/create', [ExamController::class, 'create'])->name('exams.create');//admin et teacher
                Route::post('/exams/create', [ExamController::class, 'store'])->name('exams.store');//admin et teacher
                Route::post('/exam/{exam_id}/delete', [ExamController::class, 'deleteExam'])->name('exam.delete');//admin et teacher
                Route::post('/exam/{exam_id}/{examinee_id}', [ExamController::class, 'updateExamMark'])->name('exam.updateMark');// /admin et teacher
            });
        });


        Route::get('/delete/{id}', 'delete')->where(['id' => '[0-9]+'])->middleware('admin')->name('delete');//Admin
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/my-modules', [MyModulesController::class, 'showMyModules'])->middleware('teacher_or_student')->name('modules.my_modules');//teachers et students
    Route::get('/my-demands', [ModuleController::class, 'showDemands'])->middleware('teacher')->name('demandes.liste-utilisateurs-demandes-module');//teacher
    Route::get('/my-demands/{userModuleId}/activate/{role}', [ModuleController::class, 'acceptDemand'])
        ->middleware('teacher')->name('demandes.liste-utilisateurs-demandes-module.activate');//teacher
    Route::get('/my-demands/{userModuleId}/reject/{role}', [ModuleController::class, 'rejectDemand'])
        ->middleware('teacher')->name('demandes.liste-utilisateurs-demandes-module.reject');//teacher
});



Route::patch('/module', [ModuleController::class, 'update'])->name('module.update');//all
Route::get('/module/choisir/{module_id}', [ModuleController::class, 'choisirModule'])->name('module.choisir');//all

Route::middleware('teacher_or_admin')->group(function () {
    Route::get('/histogram', [HistogramController::class, 'showHistogram'])->name('histogram.show');
});

Route::middleware('student')->group(function () {
    Route::get('/student-averages', [HistogramController::class, 'showStudentAverages'])->name('student_averages.show');
});


Route::get('/inactive-users', [AllUsersController::class, 'getInactiveUsers'])->name('inactive.users');


require __DIR__.'/auth.php';


