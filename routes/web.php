<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;

Route::get('/', function () {
    return 'Laravel OK';
});

Route::get('/hadir/{phone}', [AttendanceController::class, 'form']);
Route::post('/hadir', [AttendanceController::class, 'store']);

Route::get('/admin', function () {
    $data = \App\Models\Attendance::with('student')->get();
    return view('admin', compact('data'));
});
