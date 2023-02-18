<?php

use App\Http\Controllers\PelayaranController;
use Illuminate\Support\Facades\Route;

Route::post('pelayaran',[PelayaranController::class,'datatable'])->name('pelayaran.data');