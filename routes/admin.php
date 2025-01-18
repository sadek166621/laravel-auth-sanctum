<?php

use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

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

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AdminController::class, 'login'])->name('login');
Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');



Route::group(['middleware' => 'admin'], function () {
    // Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
});
