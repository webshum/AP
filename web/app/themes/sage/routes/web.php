<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\GalleryController;

Route::post('/chat', [ChatController::class, 'orchestrator']);
Route::post('/welcome', [ChatController::class, 'welcome']);

Route::apiResource('faqs', FaqController::class);
Route::post('/faqs/has', [FaqController::class, 'has']);

Route::post('/gallery', [GalleryController::class, 'index']);