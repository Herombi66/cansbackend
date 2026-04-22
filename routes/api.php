<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\BusinessPlanController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/contents', [ContentController::class, 'index']); // Public access
Route::get('/projects', [ProjectController::class, 'index']); // Public access
Route::get('/business-plans', [BusinessPlanController::class, 'index']); // Public access

// Public Meeting Routes
Route::middleware('throttle:5,1')->post('/meetings', [MeetingController::class, 'store']);
Route::get('/meetings/availability', [MeetingController::class, 'checkAvailability']);

// Public Marketing Routes
Route::post('/messages', [MarketingController::class, 'sendMessage']);
Route::post('/subscribers', [MarketingController::class, 'subscribe']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::get('/users', [AuthController::class, 'index']);

    // Business Plan Routes (Admin)
    Route::get('/admin/business-plans', [BusinessPlanController::class, 'adminIndex']);
    Route::post('/business-plans', [BusinessPlanController::class, 'store']);
    Route::patch('/business-plans/{id}', [BusinessPlanController::class, 'update']);
    Route::post('/business-plans/{id}/version', [BusinessPlanController::class, 'uploadNewVersion']);
    Route::delete('/business-plans/{id}', [BusinessPlanController::class, 'destroy']);

    // Meeting Routes (Admin)
    Route::get('/meetings', [MeetingController::class, 'index']);
    Route::patch('/meetings/{id}/status', [MeetingController::class, 'updateStatus']);
    Route::delete('/meetings/{id}', [MeetingController::class, 'destroy']);

    // Content Routes
    Route::post('/contents', [ContentController::class, 'store']);
    Route::delete('/contents/{content}', [ContentController::class, 'destroy']);

    // Project Routes
    Route::post('/projects', [ProjectController::class, 'store']);
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy']);

    // Marketing Routes (Admin)
    Route::get('/messages', [MarketingController::class, 'getMessages']);
    Route::get('/subscribers', [MarketingController::class, 'getSubscribers']);
    Route::post('/newsletter/send', [MarketingController::class, 'sendNewsletter']);

    // Service Routes
    Route::get('/schools', [ServiceController::class, 'schools']);
    Route::get('/hospitals', [ServiceController::class, 'hospitals']);
    Route::get('/corporates', [ServiceController::class, 'corporates']);
});
