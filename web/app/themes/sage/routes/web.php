<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\GalleryController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\TaxonomyController;

Route::post('/chat', [ChatController::class, 'orchestrator']);
Route::post('/welcome', [ChatController::class, 'welcome']);

Route::apiResource('faqs', FaqController::class);
Route::post('/faqs/has', [FaqController::class, 'has']);

Route::post('/gallery', [GalleryController::class, 'index']);

Route::apiResource('posts', PostController::class);
Route::apiResource('taxonomies', TaxonomyController::class);