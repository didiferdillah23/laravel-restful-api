<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\ContactController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/users', [UserController::class, 'register']);
Route::post('/users/login', [UserController::class, 'login']);

Route::middleware(\App\Http\Middleware\ApiAuthMiddleware::class)->group(function () {
    Route::get('/users/current', [UserController::class, 'get']);
    Route::patch('/users/current', [UserController::class, 'update']);
    Route::delete('/users/logout', [UserController::class, 'logout']);

    Route::post('/contacts', [ContactController::class, 'create']);
    Route::get('/contacts', [ContactController::class, 'search']);
    Route::get('/contacts/{id}', [ContactController::class, 'get'])->where('id', '[0-9]+'); // regex agar id hanya number
    Route::put('/contacts/{id}', [ContactController::class, 'update'])->where('id', '[0-9]+'); // regex agar id hanya number
    Route::delete('/contacts/{id}', [ContactController::class, 'delete'])->where('id', '[0-9]+'); // regex agar id hanya number

    Route::post('/contacts/{contactId}/addresses', [AddressController::class, 'create'])->where('contactId', '[0-9]+');
    Route::get('/contacts/{contactId}/addresses', [AddressController::class, 'list'])->where('contactId', '[0-9]+');
    Route::get('/contacts/{contactId}/addresses/{addressId}', [AddressController::class, 'get'])->where('contactId', '[0-9]+')->where('addressId', '[0-9]+');
    Route::put('/contacts/{contactId}/addresses/{addressId}', [AddressController::class, 'update'])->where('contactId', '[0-9]+')->where('addressId', '[0-9]+');
    Route::delete('/contacts/{contactId}/addresses/{addressId}', [AddressController::class, 'delete'])->where('contactId', '[0-9]+')->where('addressId', '[0-9]+');
});
