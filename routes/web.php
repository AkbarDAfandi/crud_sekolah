<?php

use App\Http\Controllers\ClassesController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\AttendanceController;
use Illuminate\Support\Facades\Route;

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


Route::controller(AuthController::class)->group(function(){
    Route::get('/login','index')->middleware('alreadyLoggedIn');
    Route::post('/login-user','loginUser')->name('login-user');
    Route::get('/','dashboard')->middleware('isLoggedIn')->name('dashboard');
    Route::get('/logout','logout');
    Route::get('/profile', 'profile')->middleware('isLoggedIn')->name('profile');
    Route::post('/update-profile', 'updateProfile')->middleware('isLoggedIn')->name('update-profile');
});

// Apply isLoggedIn and RoleCheck middleware to protected routes
Route::middleware(['isLoggedIn', 'role.check'])->group(function() {
    Route::resource('/classes', ClassesController::class);
    Route::get('/students/search', [StudentController::class, 'search'])->name('students.search');
    Route::get('/students/export', [StudentController::class, 'export'])->name('students.export');
    Route::resource('/students', StudentController::class);
    Route::get('/attendance/export', [AttendanceController::class, 'export'])->name('attendance.export');
    Route::get('/attendance/classPicker', [AttendanceController::class, 'classPicker'])->name('attendance.classPicker');
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/attendance/history', [AttendanceController::class, 'history'])->name('attendance.history');
    Route::post('/attendance/{student}', [AttendanceController::class, 'attendance'])->name('attendance.attendance');
    Route::resource('/subjects', SubjectController::class);
    Route::get('/teachers/search', [TeacherController::class, 'search'])->name('teachers.search');
    Route::get('/teachers/export', [TeacherController::class, 'export'])->name('teachers.export');
    Route::resource('/teachers', TeacherController::class);
});

Route::post('/image-upload', [ImageController::class, 'store'])->name('image.upload');
