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

Route::post('/register', [AuthController::class, 'register']);//register a new user
Route::post('/login', [AuthController::class, 'login']);//login

Route::group(['middleware' => ['auth:sanctum']], function() {
    Route::get('/users', [AuthController::class, 'index']);// get the list of all users
    Route::get('/orders', [OrderController::class, 'index']);// get the list of all orders
    Route::get('/products', [ProductController::class, 'index']);// get the list of products
    Route::get('/orders-details/{id}', [OrderDetailsController::class, 'show']);// get the details of a order
    Route::get('/orders/{id}', [OrderController::class, 'show']); // show a specific order
    Route::post('/orders', [OrderController::class, 'store']); // creates a order
    Route::post('/order-details', [OrderDetailsController::class, 'store']); // creates a order detail
    Route::post('/products', [ProductController::class, 'store']); // creates a product
    Route::post('/logout', [AuthController::class, 'logout']);// logout
    Route::put('/orders/{id}', [OrderController::class, 'update']);// updates a order
    Route::put('/products/{id}', [ProductController::class, 'update']);// updates a product (not affecting existing orders)
    Route::put('/order-details/{id}', [OrderDetailsController::class, 'update']);// updates a order details, affecting their assigned order
    Route::delete('/orders/{id}', [OrderController::class, 'destroy']);// set the order status to void
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);// deletes a product, not affecting order and order details
    Route::delete('/order-details/{id}', [OrderDetailsController::class, 'destroy']);// deletes a order details, affecting there assigned order
    
    Route::post('/upload',[UploadController::class, 'store']);
});