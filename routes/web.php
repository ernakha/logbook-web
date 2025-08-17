<?php

use App\Http\Controllers\ProfileController;
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

Route::get('/', function () {
    return view('welcome');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::prefix('/admin')->group(function () {
        Route::get('/dashboard', function () {
            return view('pages.admin.index');
        });
    });
});

Route::middleware(['auth', 'role:polisi'])->group(function () {
    Route::prefix('/polisi')->group(function () {
        Route::get('/dashboard', function () {
            return view('pages.polisi.index');
        });
    });
});

Route::middleware(['auth', 'role:asper'])->group(function () {
    Route::prefix('/asper')->group(function () {
        Route::get('/dashboard', function () {
            return view('pages.asper.index');
        });
    });
});

require __DIR__.'/auth.php';