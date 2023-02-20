<?php

use App\Http\Controllers\PelayaranController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('pelayaran',[PelayaranController::class,'datatable'])->name('pelayaran.data');
Route::post('user',[UserController::class,'datatable'])->name('user.data');
