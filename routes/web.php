<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('Home');
});

Route::get('/Admin', function (){
    return view('Admin.Overview');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/Register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/logout', [AuthController::class, 'logout']);

Route::get('/quen-mat-khau', function () {
    return view('dangnhapdk.quenmatkhau_b1'); 
});

Route::get('/dat-lai-mat-khau', function () {
    return view('dangnhapdk.quenmatkhau_b2'); 
});

// Các route POST xử lý logic gửi email và xác nhận
Route::post('/send-reset-code', [\App\Http\Controllers\PasswordResetController::class, 'sendCode']);
Route::post('/verify-reset-code', [\App\Http\Controllers\PasswordResetController::class, 'verifyCode']);
Route::post('/dat-lai-mat-khau', [\App\Http\Controllers\PasswordResetController::class, 'resetPassword']);