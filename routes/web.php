<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::post('/medical_history', [PatientController::class, 'store'])->name('medical_history.store');
