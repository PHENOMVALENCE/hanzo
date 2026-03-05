<?php

use App\Http\Controllers\Api\EstimateController;
use Illuminate\Support\Facades\Route;

Route::get('/estimate', [EstimateController::class, 'index']);
