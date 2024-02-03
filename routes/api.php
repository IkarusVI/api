<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\HostController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\LoginController;


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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('admin')->controller(AdminController::class)->group(function () {

    Route::get('/'          ,'getAll');
    Route::get('/{email}'   ,'getOne')->middleware('validate.email');
    Route::post('/'         ,'create');
    Route::put('/{email}'   ,'modify')->middleware('validate.email');;
    Route::delete('/{email}','delete')->middleware('validate.email');

});

Route::prefix('host')->controller(HostController::class)->group(function () {

    Route::get('/'          ,'getAll');
    Route::get('/{email}'   ,'getOne')->middleware('validate.email');
    Route::post('/'         ,'create');
    Route::put('/{email}'   ,'modify')->middleware('validate.email');;
    Route::delete('/{email}','delete')->middleware('validate.email');

});

Route::prefix('guest')->controller(GuestController::class)->group(function () {

    Route::get('/'          ,'getAll');
    Route::get('/{email}'   ,'getOne')->middleware('validate.email');
    Route::post('/'         ,'create');
    Route::put('/{email}'   ,'modify')->middleware('validate.email');;
    Route::delete('/{email}','delete')->middleware('validate.email');

});


Route::post('/login', [LoginController::class,'login']);

Route::get('/logout', [LoginController::class,'killToken'])->middleware('validate.login');;

Route::get('/profile',[LoginController::class,'identify'])->middleware('validate.login');