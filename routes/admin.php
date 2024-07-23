<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;
use App\Http\Livewire\Admin as LvAdmin;
use App\Http\Livewire\Components;
use App\Http\Livewire\Master;
use App\Http\Livewire\Report as LvReport;
use App\Http\Livewire\Transaction as LvTransaction;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "admin" middleware group. Now create something great!
|
*/

Route::middleware([
    'auth:admin',
    // config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::get('/home', LvAdmin\Dashboard::class)->name('admin.dashboard');

    Route::prefix('master')->name('master.')->group(function () {
        Route::get('/teacher', Master\LvTeacher::class)->name('teacher');
        Route::post('/teacher/dt-get', [Master\LvTeacher::class, 'dtTeacher'])->name('teacher.datatables');

        Route::get('/student', Master\LvStudent::class)->name('student');
        Route::post('/student/dt-get', [Master\LvStudent::class, 'dtStudent'])->name('student.datatables');

        Route::prefix('law')->name('law.')->group(function () {
            Route::get('/violation', Master\LvViolation::class)->name('violation');
            Route::post('/violation/dt-get', [Master\LvViolation::class, 'dtViolation'])->name('violation.datatables');

            Route::get('/sanction', Master\LvSanction::class)->name('sanction');
            Route::post('/sanction/dt-get', [Master\LvSanction::class, 'dtSanction'])->name('sanction.datatables');
        });

        Route::get('/achievement', Master\LvAchievement::class)->name('achievement');
        Route::post('/achievement/dt-get', [Master\LvAchievement::class, 'dtAchievement'])->name('achievement.datatables');
    });

    Route::prefix('record')->name('record.')->group(function () {
        Route::get('/record/student-violation', LvTransaction\LvStudentViolation::class)->name('student.violation');
        Route::get('/record/student-violation/select-get', [LvTransaction\LvStudentViolation::class, 'slcViolation'])->name('student.violation.select2');
        Route::post('/record/student-violation/dt-get', [LvTransaction\LvStudentViolation::class, 'dtViolation'])->name('student.violation.datatables');

        Route::get('/record/student-sanction', LvTransaction\LvStudentSanction::class)->name('student.sanction');
        Route::get('/record/student-sanction/select-get', [LvTransaction\LvStudentSanction::class, 'slcSanction'])->name('student.sanction.select2');
        Route::post('/record/student-sanction/dt-get', [LvTransaction\LvStudentSanction::class, 'dtSanction'])->name('student.sanction.datatables');

        Route::get('/record/student-achievement', LvTransaction\LvStudentAchievement::class)->name('student.achievement');
        Route::get('/record/student-achievement/select-get', [LvTransaction\LvStudentAchievement::class, 'slcAchievement'])->name('student.achievement.select2');
        Route::post('/record/student-achievement/dt-get', [LvTransaction\LvStudentAchievement::class, 'dtAchievement'])->name('student.achievement.datatables');
    });

    Route::prefix('report')->name('report.')->group(function () {
        Route::get('/violation', LvReport\LvRpViolation::class)->name('violation');
        Route::post('/violation/dt-get', [LvReport\LvRpViolation::class, 'dtRpViolation'])->name('violation.datatables');
   
        Route::get('/sanction', LvReport\LvRpSanction::class)->name('sanction');
        Route::post('/sanction/dt-get', [LvReport\LvRpSanction::class, 'dtRpSanction'])->name('sanction.datatables');
    
        Route::get('/achievement', LvReport\LvRpAchievement::class)->name('achievement');
        Route::post('/achievement/dt-get', [LvReport\LvRpAchievement::class, 'dtRpAchievement'])->name('achievement.datatables');
    });

    Route::prefix('component-requests')->name('component.')->group(function () {
        Route::post('violation/dt-get', [Components\LvModalViolation::class, 'dtViolation'])->name('violation.datatables');
        Route::post('sanction/dt-get', [Components\LvModalSanction::class, 'dtSanction'])->name('sanction.datatables');
        Route::post('achievement/dt-get', [Components\LvModalAchievement::class, 'dtAchievement'])->name('achievement.datatables');
    });

    Route::post('/logout', [Admin\LoginController::class, 'logout'])->name('admin.logout');
});

Route::get('/login', [Admin\LoginController::class, 'viewLogin'])->name('admin.login');
Route::post('/login', [Admin\LoginController::class, 'login'])->name('admin.login.create');
