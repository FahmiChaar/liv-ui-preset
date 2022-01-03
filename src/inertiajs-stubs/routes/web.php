<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\UserController;


Route::redirect('/', 'dashboard');

Route::group([
    'middleware' => ['auth'],
    'prefix' => 'dashboard',
    'as' => 'dashboard.',
], function() {
    Route::get('/', [DashboardController::class, 'index'])->name('home');
    Route::resource('users', [UserController::class]);
});

require __DIR__.'/auth.php';
