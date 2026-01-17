<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AdminController;

// Landing Page
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
Route::post('/check-attendance-time', [AttendanceController::class, 'checkAttendanceTime'])->name('check.attendance.time');

// Admin Routes
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
    
    // Attendance Time Settings
    Route::get('/attendance-time-settings', [AdminController::class, 'attendanceTimeSettings'])->name('admin.time.settings');
    Route::post('/attendance-time-settings/create', [AdminController::class, 'createTimeSetting'])->name('admin.time.settings.create');
    Route::post('/attendance-time-settings/update/{id}', [AdminController::class, 'updateTimeSetting'])->name('admin.time.settings.update');
    Route::delete('/attendance-time-settings/delete/{id}', [AdminController::class, 'deleteTimeSetting'])->name('admin.time.settings.delete');
    Route::get('/get-active-time-settings', [AdminController::class, 'getActiveTimeSettings'])->name('admin.get.time.settings');
    
    // Debug Route
    Route::get('/debug-time', [AdminController::class, 'debugTime'])->name('admin.debug.time');
});

// Simple test route (tanpa auth)
Route::get('/test-time', function() {
    return response()->json([
        'time' => now()->format('Y-m-d H:i:s'),
        'day' => now()->dayOfWeekIso,
        'day_name' => now()->translatedFormat('l')
    ]);
});

// Catch-all untuk admin routes yang tidak valid
Route::get('/admin/*', function () {
    if (session()->has('user_id') && session('user_role') === 'admin') {
        return redirect('/admin');
    }
    return redirect('/login')->with('error', 'Silakan login terlebih dahulu.');
});