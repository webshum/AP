<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\FaqController;

Route::post('/chat', [ChatController::class, 'handle']);

Route::apiResource('faqs', FaqController::class);