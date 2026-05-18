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
| Root
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| AJAX (public)
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
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // =========================================================
    // LITMAS — urutan: static dulu, parameter belakang
    // =========================================================

    // 1. Static tanpa parameter
    Route::get('/litmas',        [LitmasController::class, 'index'])->name('litmas.index');
    Route::get('/litmas/create', [LitmasController::class, 'create'])->name('litmas.create');
    Route::get('/litmas/form',   [LitmasController::class, 'form'])->name('litmas.form');
    Route::post('/litmas',       [LitmasController::class, 'store'])->name('litmas.store');
    Route::post('/litmas/store', [ExportController::class, 'store'])->name('export.store');

    // 2. Prefix tetap dengan parameter di belakang
    Route::get('/litmas/preview/{id}', [ExportController::class, 'preview'])->name('export.preview');
    Route::get('/litmas/word/{id}',    [ExportController::class, 'exportWord'])->name('export.word');

    // 3. Route dengan {id} dinamis — harus setelah semua static
    Route::get('/litmas/{id}/edit',  [LitmasController::class, 'edit'])->name('litmas.edit');
    Route::put('/litmas/{id}',       [LitmasController::class, 'update'])->name('litmas.update');
    Route::delete('/litmas/{id}',    [LitmasController::class, 'destroy'])->name('litmas.destroy');

    // 4. Jenis litmas — paling bawah
    Route::get('/litmas/{jenis}', [LitmasController::class, 'pilihJenis'])
        ->where('jenis', 'anak|dewasa|awal')
        ->name('litmas.jenis');

    // =========================================================
    // CLIENT
    // =========================================================
    Route::resource('clients', ClientController::class)->except(['destroy']);

    // AJAX PENJAMIN
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

    Route::get('/manajemen-user',                   [UserController::class, 'index'])->name('users.index');
    Route::get('/manajemen-user/create',            [UserController::class, 'create'])->name('users.create');
    Route::post('/manajemen-user',                  [UserController::class, 'store'])->name('users.store');
    Route::get('/manajemen-user/{user}/edit',       [UserController::class, 'edit'])->name('users.edit');
    Route::put('/manajemen-user/{user}',            [UserController::class, 'update'])->name('users.update');
    Route::delete('/manajemen-user/{user}',         [UserController::class, 'destroy'])->name('users.destroy');
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