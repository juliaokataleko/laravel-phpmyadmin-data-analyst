<?php

use App\Http\Controllers\IndexController;
use App\Models\EmployeeDemographics;
use Illuminate\Support\Facades\DB;
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

Route::get('/', [IndexController::class, 'index']);
Route::get('/join', [IndexController::class, 'join']);
Route::get('/union', [IndexController::class, 'union']);
Route::get('/case', [IndexController::class, 'case']);
Route::get('/having', [IndexController::class, 'having']);
Route::get('/crud', [IndexController::class, 'crud']);
Route::get('/aliasing', [IndexController::class, 'aliasing']);
Route::get('/partition', [IndexController::class, 'partition']);
