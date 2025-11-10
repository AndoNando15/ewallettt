<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DataAdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DatasetController;
use App\Http\Controllers\ProsesController;
use Illuminate\Support\Facades\Route;


Route::middleware('guest')->group(function () {

    Route::get('/', [AuthController::class, 'index']);  // Add this line
    Route::get('/login', [AuthController::class, 'index'])->name('login');  // Add this line
    Route::post('/loginForm', [AuthController::class, 'loginForm'])->name('loginForm');
});

Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::resource('data-admin', DataAdminController::class);
    Route::resource('dataset', DatasetController::class);
    Route::post('dataset/import', [DatasetController::class, 'import'])->name('dataset.import');
    Route::resource('proses', ProsesController::class);
    // Custom route for fetching datasets based on selected cluster
// Custom route for form submission to process selected datasets

    Route::post('proses/cluster', [ProsesController::class, 'process'])->name('proses.cluster');

    // Custom route to fetch datasets based on selected cluster
    Route::get('proses/datasets/{cluster}', [ProsesController::class, 'getDatasetsByCluster']);
});
Route::fallback(function () {
    return view('error404');
});