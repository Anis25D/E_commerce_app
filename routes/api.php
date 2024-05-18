<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\MessageController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


# Auth routes 
Route::controller(AuthController::class)->group(function() {
    // Route::post('/register', 'register');
    Route::post('/registerSeller', 'registerSeller');
    Route::post('/registerCustomer', 'registerCustomer');
    Route::post('/registerAdmin', 'registerAdmin');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->middleware('auth:sanctum');
    
});

Route::prefix('product')->controller(ProductController::class)->group(function() {
    Route::post('/create', 'create_product');
    Route::get('/getAll', 'get_all_products');
    Route::post('/update/{product_id}', 'get_all_products');
    Route::get('/getByCategory/{category_id}', 'get_products_by_category');
    Route::get('/countProducts', 'count_products');
    Route::delete('/delete/{id}', 'delete_product');

});

Route::prefix('Category')->controller(CategoryController::class)->group(function() {
    Route::post('/create', 'newCategory');
    Route::get('/get', 'getAllCategories');
   

});




Route::prefix('admin')->controller(AdminController::class)->group(function() {
    Route::get('/getUsers', 'get_users');
    Route::get('/getSellers', 'get_sellers');
    Route::get('/getCustomers', 'get_customers');
    Route::get('/getNotifications', 'get_notifications');
    Route::put('/seeAllNotifications', 'mark_notifications_seen');
    Route::get('/getUser/{id}', 'get_user');
 
   
});

Route::prefix('user')->controller(UserController::class)->group(function() {
    Route::delete('/deleteUser/{id}', 'deleteUser');
    Route::put('/updateUser/{id}', 'updateUser');
    
});


Route::prefix('message')->controller(MessageController::class)->group(function() {

        Route::get('/testEvent', 'testEvent');
        Route::post('/sendMessage', 'sendMessage');
    
});

Route::prefix('notification')->controller(NotificationController::class)->group(function() {

    Route::post('/createNotification', 'createNotification');
    Route::put('/markNotificationSeen/{id}', 'markNotificationSeen');
    

});




