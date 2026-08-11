<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PresetController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/presets', [PresetController::class, 'index']);
Route::get('/presets/{id}', [PresetController::class, 'show']);
Route::post('/presets', [PresetController::class, 'store']);
Route::delete('/presets/{id}', [PresetController::class, 'destroy']);
