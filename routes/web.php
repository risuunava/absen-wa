<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AdminController;

// Landing Page - accessible to everyone
Route::get('/', function () {
    $school = \App\Models\School::first();
    $userRole = session('user_role');
    $userName = session('user_name');
    $fullName = session('full_name');
    
    // Check if user has already attended today
    $hasAttendedToday = false;
    if (session()->has('user_id')) {
        $hasAttendedToday = \App\Models\Attendance::where('user_id', session('user_id'))
            ->whereDate('date', now()->toDateString())
            ->exists();
    }
    
    return view('landing', compact('school', 'userRole', 'userName', 'fullName', 'hasAttendedToday'));
})->name('home');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Attendance Routes
Route::post('/hadir', [AttendanceController::class, 'hadir'])->name('hadir');
Route::post('/get-distance', [AttendanceController::class, 'getDistance'])->name('get.distance');

// Admin Routes - PROTECTED manually without middleware
Route::prefix('admin')->group(function () {
    // Dashboard
    Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // User Management
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::post('/users/create', [AdminController::class, 'createUser'])->name('admin.users.create');
    Route::post('/users/edit/{id}', [AdminController::class, 'editUser'])->name('admin.users.edit');
    Route::delete('/users/delete/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
    
    // Attendance Management
    Route::get('/attendance', [AdminController::class, 'attendance'])->name('admin.attendance');
    Route::post('/attendance/{id}/verify-photo', [AdminController::class, 'verifyPhoto'])->name('admin.verify.photo');
    Route::delete('/attendance/{id}', [AdminController::class, 'deleteAttendance'])->name('admin.attendance.delete');
    
    // School Configuration
    Route::post('/school/update', [AdminController::class, 'updateSchool'])->name('admin.school.update');
});

// Catch-all untuk admin routes yang tidak valid
Route::get('/admin/*', function () {
    if (session()->has('user_id') && session('user_role') === 'admin') {
        return redirect('/admin');
    }
    return redirect('/login')->with('error', 'Silakan login terlebih dahulu.');
});