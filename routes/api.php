<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Resources\AgentResource;
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
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AgentAuthController;
use App\Http\Middleware\AdminAuth;
use App\Http\Resources\UserResource;

Route::middleware('throttle:guest')->group(function(){

    //admin auth endpoints
Route::post('/admin/register', [AdminAuthController::class, 'registerAdmin']);
Route::post('/admin/login', [AdminAuthController::class, 'loginAdmin']);

});





/**
 *
 * 
 * This route will return the authenticated user information.
 *
 * This will work for both users and admins
 * make a call to this endpoint to get the user details/object
 * then you can store both the token and user details in local storage or however you wish to.
 * 
 */

Route::get('/user-profile', function (Request $request) {
    return new UserResource($request->user());
})->middleware(['auth:sanctum', 'throttle:api']);

/**
 *
 * 
 * This route will return the authenticated agent information.
 *
 * This will work for only agents
 * make a call to this endpoint to get the agent details/object
 * then you can store both the token and agent details in local storage or however you wish to.
 * 
 */

Route::get('/agent-profile', function (Request $request) {
    return new AgentResource($request->user());
})->middleware(['auth:api-agent', 'throttle:api']);


/**
 *
 * 
 * These are the publicly Available user registration and login routes.
 *
 * T
 * It's a really useful endpoint, and you should play around 
 * with it for a bit.
 * 
 */




/*
|--------------------------------------------------------------------------
| PUBLIC AUTH ROUTES – USERS
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'registerUser']);
    Route::post('login', [AuthController::class, 'loginUser']);

    Route::post('forgot-password', [UserForgotPasswordController::class, 'sendResetLinkEmail']);
    Route::post('reset-password', [UserForgotPasswordController::class, 'resetPassword']);

    Route::post('email/send', [UserEmailVerificationController::class, 'sendVerificationOTP']);
    Route::post('email/verify', [UserEmailVerificationController::class, 'verify']);
})->middleware('throttle:guest');


/*
|--------------------------------------------------------------------------
| PUBLIC AUTH ROUTES – AGENTS
|--------------------------------------------------------------------------
*/
Route::prefix('agent')->group(function () {
    Route::post('register', [AgentAuthController::class, 'registerAgent']);
    Route::post('login', [AgentAuthController::class, 'loginAgent']);

    Route::post('forgot-password', [AgentForgotPasswordController::class, 'sendResetLinkEmail']);
    Route::post('reset-password', [AgentForgotPasswordController::class, 'resetPassword']);

    Route::post('email/send', [AgentEmailVerificationController::class, 'sendVerificationOTP']);
    Route::post('email/verify', [AgentEmailVerificationController::class, 'verify']);
})->middleware('throttle:guest');


/*
|--------------------------------------------------------------------------
| PUBLIC PROPERTY ROUTES
|--------------------------------------------------------------------------
*/
Route::prefix('properties')->middleware('throttle:public-properties')
->group(function () {
    Route::get('/', [PropertyController::class, 'index']);
    Route::get('search', [PropertyController::class, 'searchProperties']); // static first
    Route::get('{property}', [PropertyController::class, 'show']);
    Route::get('{property}/virtual-tour', [PropertyController::class, 'getPropertyVideos']);
});


/*
|--------------------------------------------------------------------------
| PUBLIC CAREERS ROUTES
|--------------------------------------------------------------------------
*/
Route::prefix('careers')->middleware('throttle:guest')
->group(function () {
    Route::get('/', [CareerController::class, 'index']);
    Route::get('search', [CareerController::class, 'search']); // static first
    Route::get('{career}', [CareerController::class, 'show']);
});



/*
|--------------------------------------------------------------------------
| AUTHENTICATED USER ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {

    Route::post('auth/logout', [AuthController::class, 'logoutUser']);

    Route::post('careers/apply', [CareerController::class, 'sendJobApplication']);
    Route::prefix('inquiries')->group(function () {
        Route::post('/{property}', [InquiryController::class, 'store']);
        Route::get('/my-inquiries', [InquiryController::class, 'getUserInquiries']);
    });

});


/*
|--------------------------------------------------------------------------
| AUTHENTICATED AGENT ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:api-agent', 'throttle:api'])->group(function () {

    Route::post('agent/logout', [AgentAuthController::class, 'logoutAgent']);

    Route::get('agent/dashboard', [AgentController::class, 'agentDashboard']);

    /*
    |--------------------------------------------------------------------------
    | Agent Self Management
    |--------------------------------------------------------------------------
    */
    Route::prefix('agents')->group(function () {
       

        Route::post('start-verification/{agent}', [AgentController::class, 'startVerification']);
        Route::post('upload-verification/{agent}', [AgentController::class, 'uploadVerificationDocument']);
    });

});


/*
|--------------------------------------------------------------------------
| VERIFIED AGENT PROPERTY MANAGEMENT
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:api-agent', 'agent.verified', 'throttle:api'])->group(function () {

    Route::prefix('properties')->group(function () {
        Route::post('/', [PropertyController::class, 'store']);
        Route::get('/my-properties', [AgentController::class, 'getAgentProperties']);
        Route::post('{property}/upload-media', [PropertyController::class, 'uploadMedia']);

        Route::put('{property}', [PropertyController::class, 'update']);
        Route::patch('{property}', [PropertyController::class, 'update']);
        Route::delete('{property}', [PropertyController::class, 'destroy']);
    });

});

/*
|--------------------------------------------------------------------------
| SHARED ADMIN & AGENT ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware('admin.agent')->group(function () {

    Route::get('agents/{agent}', [AgentController::class, 'show']);
    Route::get('agents/{agent}/my-properties', [AgentController::class, 'getAgentProperties']);
    Route::get('properties/{property}/inquiries', [
        PropertyController::class,
        'getPropertyInquiries'
    ]);

});


/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware('admin')->group(function () {

    Route::post('admin/logout', [AdminAuthController::class, 'logoutAdmin']);
    Route::get('admin/dashboard', [AgentController::class, 'adminSummary']);

    /*
    |--------------------------------------------------------------------------
    | Agent Administration
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin/agents')->group(function () {
        Route::get('/', [AgentController::class, 'index']);
        Route::get('unverified', [AgentController::class, 'getUnverifiedAgents']);
        Route::get('verified', [AgentController::class, 'getVerifiedAgents']);
        Route::get('rejected', [AgentController::class, 'getRejectedAgents']);
        Route::post('verify-agent/{agent}', [AgentController::class, 'verifyAgent']);
        Route::get('agent-with-properties', [AgentController::class, 'agentsWithProperties']);
    });

    /*
    |--------------------------------------------------------------------------
    | Career Management
    |--------------------------------------------------------------------------
    */
   
     Route::prefix('careers')->group(function () {
        Route::post('/', [CareerController::class, 'store']);
        Route::put('{career}', [CareerController::class, 'update']);
        Route::patch('{career}', [CareerController::class, 'update']);

        Route::delete('{career}', [CareerController::class, 'delete']);
        Route::delete('{career}/destroy', [CareerController::class, 'destroy']);

        Route::patch('{id}/restore', [CareerController::class, 'restore']);
        Route::get('get-deleted', [CareerController::class, 'getDeleted']);
        Route::patch('{career}/toggle', [CareerController::class, 'careerToggle']);
    });

    /*
    |--------------------------------------------------------------------------
    | Inquiry Moderation
    |--------------------------------------------------------------------------
    */
    Route::prefix('inquiries')->group(function () {
        Route::get('/', [InquiryController::class, 'index']);
        Route::get('{inquiry}', [InquiryController::class, 'show']);
        Route::put('{inquiry}', [InquiryController::class, 'update']);
        Route::patch('{inquiry}', [InquiryController::class, 'update']);
        Route::delete('{inquiry}', [InquiryController::class, 'destroy']);
    });

});











