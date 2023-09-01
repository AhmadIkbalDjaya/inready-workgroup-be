<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\CategoryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('category', [CategoryController::class, 'index']);
Route::resource('article', ArticleController::class)->except(['create', 'edit']);
Route::resource('agenda', AgendaController::class)->except(['create', 'edit']);
Route::resource('activity', ActivityController::class)->except(['create', 'edit']);
