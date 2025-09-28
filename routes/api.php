<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\OnlineUser;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

// Check if a user is online
Route::get('/users/{user}/status', function (User $user) {
    return response()->json([
        'online' => $user->isOnline(),
        'last_seen' => $user->last_seen
    ]);
});

Route::get('/check-verification/{user}', [\App\Http\Controllers\Auth\VerificationController::class, 'checkStatus']);

// API routes for messages
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/conversations/{conversation}/messages', [\App\Http\Controllers\MessageController::class, 'getMessages'])
        ->name('api.messages.index');
});
