<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderDetailsController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UploadController;

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

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function() {
    Route::get('/users', [AuthController::class, 'index']);//
    Route::get('/orders', [OrderController::class, 'index']);//
    Route::get('/products', [ProductController::class, 'index']);//
    Route::get('/orders-details/{id}', [OrderDetailsController::class, 'show']);//
    Route::get('/orders/{id}', [OrderController::class, 'show']); //
    Route::post('/orders', [OrderController::class, 'store']); //
    Route::post('/order-details', [OrderDetailsController::class, 'store']);
    Route::post('/products', [ProductController::class, 'store']); //
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/orders/{id}', [OrderController::class, 'update']);//
    Route::put('/products/{id}', [ProductController::class, 'update']);//
    Route::put('/order-details/{id}', [OrderDetailsController::class, 'update']);//
    Route::delete('/orders/{id}', [OrderController::class, 'destroy']);//
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);//
    Route::delete('/order-details/{id}', [OrderDetailsController::class, 'destroy']);
    
    Route::post('/upload',[UploadController::class, 'store']);
});