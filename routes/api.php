<?php

use App\Http\Controllers\Api\All\Card\CardController;
use App\Http\Controllers\Api\All\Category\CategoryController;
use App\Http\Controllers\Api\All\Order\CustomOrderController;
use App\Http\Controllers\Api\All\Product\ProductController;
use App\Http\Controllers\Api\All\SCategory\SubCategoryController;
use App\Http\Controllers\Api\All\Slider\CatSliderController;
use App\Http\Controllers\Api\All\Slider\SliderController;
use App\Http\Controllers\Api\All\User\Auth\ForgetPassController;
use App\Http\Controllers\Api\All\User\Auth\LoginController;
use App\Http\Controllers\Api\All\User\Auth\LogoutController;
use App\Http\Controllers\Api\All\User\Auth\OtpController;
use App\Http\Controllers\Api\All\User\Auth\RegisterController;
use App\Http\Controllers\Api\All\User\Auth\RemoveAccountController;
use App\Http\Controllers\Api\All\User\Profile\ShowController;
use App\Http\Controllers\Api\All\User\Profile\UpdateController;
use Illuminate\Support\Facades\Route;

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

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});


Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/profile', [ShowController::class, 'index']);
    Route::put('/profile/update', [UpdateController::class, 'update']);
    Route::put('/profile/password', [UpdateController::class, 'updatePassword']);
    Route::get('/removeUser', [RemoveAccountController::class, 'removeAccount']);
    Route::post('/logout', [LogoutController::class, 'logout']);
    Route::get('/cards', [CardController::class, 'index']);
    Route::post('/cards/create', [CardController::class, 'store']);
});

//public routes
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);
Route::post('/verify', [OtpController::class, 'verifyOTP']);
Route::post('/resend', [OtpController::class, 'resendOTP']);
Route::post('/forget-password', [ForgetPassController::class, 'forgetPassword']);
Route::post('/reset-password', [ForgetPassController::class, 'resetPassword']);

//cities
Route::get('/cites', [RegisterController::class, 'allCity']);
Route::post('/cites/create', [RegisterController::class, 'addCity']);


//slider
Route::get('/slider', [SliderController::class, 'index']);
Route::get('/catSlider', [CatSliderController::class, 'index']);
Route::get('/iSlider', [SliderController::class, 'index']);


//categories
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);

//sub categories
Route::get('/sub-categories/cat/{id}', [SubCategoryController::class, 'index']);
Route::get('/sub-categories/{id}', [SubCategoryController::class, 'show']);

//products
Route::get('/product/{id}', [ProductController::class, 'index']);
Route::get('/product', [ProductController::class, 'getAll']);

//custom order
Route::post('/custom-order', [CustomOrderController::class, 'customOrder']);

//remove this later
Route::post('/slider/create', [SliderController::class, 'store']);
Route::post('/catSlider/create', [CatSliderController::class, 'store']);
Route::post('/iSlider/create', [SliderController::class, 'store']);
Route::post('/categories/create', [CategoryController::class, 'store']);
Route::post('/sub-categories/create', [SubCategoryController::class, 'store']);
Route::post('/product/create',[ProductController::class,'store']);
