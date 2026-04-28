<?php

use App\Http\Controllers\ExportController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LitmasController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\GuarantorController;
use App\Http\Controllers\PasalController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Root
Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| AJAX (sementara di luar auth untuk debug)
|--------------------------------------------------------------------------
*/
Route::get('/ajax/keluarga/{client}', [LitmasController::class, 'getKeluarga']);


/*
|--------------------------------------------------------------------------
| SEMUA USER LOGIN
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Litmas
    Route::get('/litmas', [LitmasController::class, 'index'])->name('litmas.index');
    Route::get('/litmas/create', [LitmasController::class, 'create'])->name('litmas.create');
    Route::post('/litmas', [LitmasController::class, 'store'])->name('litmas.store');

    Route::get('/litmas/form', [LitmasController::class, 'form'])->name('litmas.form');

    Route::get('/litmas/{jenis}', [LitmasController::class, 'pilihJenis'])
        ->where('jenis', 'anak|dewasa|awal')
        ->name('litmas.jenis');

    // Export Dan Download Litmas
    Route::post('/litmas/store', [ExportController::class, 'store'])->name('export.store');
    Route::get('/litmas/preview/{id}', [ExportController::class, 'preview'])->name('export.preview');
    Route::get('/litmas/word/{id}', [ExportController::class, 'exportWord'])->name('export.word');
    Route::get('/litmas/pdf/{id}', [ExportController::class, 'exportPDF'])->name('export.pdf');

    // CLIENT
    Route::resource('clients', ClientController::class)->except(['destroy']);

    // AJAX PENJAMIN (cukup 1 saja)
    Route::get('ajax/penjamin/{client}', [GuarantorController::class, 'ajaxByClient'])
        ->name('penjamin.ajax');
});


/*
|--------------------------------------------------------------------------
| USER BIASA
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/litmas-saya', [LitmasController::class, 'myLitmas'])
        ->name('litmas.my');
});


/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->group(function () {

    Route::delete('/clients/{client}', [ClientController::class, 'destroy'])
        ->name('clients.destroy');

    Route::resource('pasal', PasalController::class);

    Route::get('/manajemen-user', [UserController::class, 'index'])->name('users.index');
    Route::get('/manajemen-user/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/manajemen-user', [UserController::class, 'store'])->name('users.store');
    Route::get('/manajemen-user/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/manajemen-user/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/manajemen-user/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::post('/manajemen-user/{user}/reset-password', [UserController::class, 'resetPassword'])
        ->name('users.reset-password');
});


/*
|--------------------------------------------------------------------------
| PENJAMIN
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    Route::resource('penjamin', GuarantorController::class);

    Route::get('klien/{client}/penjamin', [GuarantorController::class, 'byClient'])
        ->name('penjamin.byClient');

    Route::delete('penjamin/{penjamin}/force', [GuarantorController::class, 'forceDelete'])
        ->name('penjamin.forceDelete');
});

// Auth
require __DIR__.'/auth.php';