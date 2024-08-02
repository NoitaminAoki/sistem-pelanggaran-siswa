<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Student;
use App\Http\Livewire\Student as LvStudent;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});




Route::middleware([
    'auth:studentUser',
    // config('jetstream.auth_session'),
    'verified'
])->name('student.')->group(function () {

    Route::prefix('record')->name('record.')->group(function () {
        Route::get('/record/student-violation', LvStudent\LvReportViolation::class)->name('violation');
        Route::post('/record/student-violation/dt-get', [LvStudent\LvReportViolation::class, 'dtViolation'])->name('violation.datatables');

        Route::get('/record/student-sanction', LvStudent\LvReportSanction::class)->name('sanction');
        Route::post('/record/student-sanction/dt-get', [LvStudent\LvReportSanction::class, 'dtSanction'])->name('sanction.datatables');

        Route::get('/record/student-achievement', LvStudent\LvReportAchievement::class)->name('achievement');
        Route::post('/record/student-achievement/dt-get', [LvStudent\LvReportAchievement::class, 'dtAchievement'])->name('achievement.datatables');
    });

    Route::get('/home', LvStudent\Dashboard::class)->name('dashboard');
    Route::post('/logout', [Student\LoginController::class, 'logout'])->name('logout');
});

Route::get('/login', [Student\LoginController::class, 'viewLogin'])->name('student.login');
Route::post('/login', [Student\LoginController::class, 'login'])->name('student.login.create');
