<?php

use App\Http\Controllers\Assist\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('test_assist', [DashboardController::class, 'index']);
