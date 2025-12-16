<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\CareerController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\AgentForgotPasswordController;
use App\Http\Controllers\UserForgotPasswordController;
use App\Http\Controllers\AgentEmailVerificationController;
use App\Http\Controllers\UserEmailVerificationController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//Auth Routes
Route::post('auth/register', [App\Http\Controllers\AuthController::class, 'registerUser']);
Route::post('auth/login', [App\Http\Controllers\AuthController::class, 'loginUser']);
Route::post('auth/logout', [App\Http\Controllers\AuthController::class, 'logoutUser'])->middleware('auth:sanctum');

Route::post('agent-auth/register', [App\Http\Controllers\AgentAuthController::class, 'registerAgent']);
Route::post('agent-auth/login', [App\Http\Controllers\AgentAuthController::class, 'loginAgent']);
Route::post('agent-auth/logout', [App\Http\Controllers\AgentAuthController::class, 'logoutAgent'])->middleware('auth:api-agent');

// USERS PASSWORD RESET
Route::post('/auth/forgot-password', [UserForgotPasswordController::class, 'sendResetLinkEmail']);
Route::post('/auth/reset-password', [UserForgotPasswordController::class, 'resetPassword']);

// AGENTS PASSWORD RESET
Route::post('/agent/forgot-password', [AgentForgotPasswordController::class, 'sendResetLinkEmail']);
Route::post('/agent/reset-password', [AgentForgotPasswordController::class, 'resetPassword']);

// USERS EMAIL VERIFICATION
Route::post('/auth/email/send', [UserEmailVerificationController::class, 'sendVerificationOTP']);
Route::post('/auth/email/verify', [UserEmailVerificationController::class, 'verify']);

// AGENTS EMAIL VERIFICATION
Route::post('/agent/email/send', [AgentEmailVerificationController::class, 'sendVerificationOTP']);
Route::post('/agent/email/verify', [AgentEmailVerificationController::class, 'verify']);




// Route::apiResource('inquiries', App\Http\Controllers\InquiryController::class);
// Route::apiResource('careers', App\Http\Controllers\CareerController::class);
// Route::apiResource('properties', App\Http\Controllers\PropertyController::class);
// Route::apiResource('agents', App\Http\Controllers\AgentController::class);


//Agent endpoints will be visible to authenticated agents and admin
// Agent endpoints
Route::prefix('agents')->group(function () {
    //Route::get('/', [AgentController::class, 'index']);
    Route::post('/', [AgentController::class, 'store']);
    Route::get('/{agent}', [AgentController::class, 'show']);
    Route::put('/{agent}', [AgentController::class, 'update']);
    Route::patch('/{agent}', [AgentController::class, 'update']);
    Route::delete('/{agent}', [AgentController::class, 'destroy']);
    Route::post('/start-verification/{agent}', [AgentController::class, 'uploadVerificationDocuments']);
    //missing routes
    //get all agent's properties
    //
   
});

// Property endpoints
Route::prefix('properties')->group(function () {
    Route::get('/', [PropertyController::class, 'index']);
    Route::post('/', [PropertyController::class, 'store']);
    Route::get('/{property}', [PropertyController::class, 'show']);
    Route::put('/{property}', [PropertyController::class, 'update']);
    Route::patch('/{property}', [PropertyController::class, 'update']);
    Route::delete('/{property}', [PropertyController::class, 'destroy']);
    //missing routes
    //get all inquiries for a property
    //get all media for a property
});

// Careers (jobs) endpoints
Route::prefix('careers')->group(function () {
    Route::get('/', [CareerController::class, 'index']);
    Route::post('/', [CareerController::class, 'store']);
    Route::get('/{career}', [CareerController::class, 'show']);
    Route::put('/{career}', [CareerController::class, 'update']);
    Route::patch('/{career}', [CareerController::class, 'update']);
    Route::delete('/{career}', [CareerController::class, 'destroy']);
});

// Inquiry endpoints
Route::prefix('inquiries')->group(function () {
    Route::get('/', [InquiryController::class, 'index']);
    Route::post('/', [InquiryController::class, 'store']);
    Route::get('/{inquiry}', [InquiryController::class, 'show']);
    Route::put('/{inquiry}', [InquiryController::class, 'update']);
    Route::patch('/{inquiry}', [InquiryController::class, 'update']);
    Route::delete('/{inquiry}', [InquiryController::class, 'destroy']);
});

//Admin endpoints
Route::prefix('admin')->group(function () {
    Route::post('/verify-agent/{agent}', [App\Http\Controllers\AdminController::class, 'verifyAgent']);

     //missing routes
    //get all agent's properties
    //get all verified agents
    //get all unverified agents
    //
});