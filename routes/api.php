<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\LoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::controller(LoginController::class)->group(function () {
        Route::post('register', 'store');
        Route::post('login', 'login');
        Route::post('logout', 'logout');
        Route::get('user', 'index');

    });
    Route::controller(CourseController::class)->group(function () {
        Route::get('/courses', 'index');
        Route::post('/courses', 'store');
        Route::get('/courses/{id}', 'show');
        Route::post('/courses/{id}/enroll', 'enroll');
        Route::delete('/courses/{id}/unenroll', 'enroll');

    });
});
