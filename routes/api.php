<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\HostController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\HouseController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\StripePaymentController;
use Laravel\Socialite\Facades\Socialite;


/* 
Route::get('/google-auth/redirect', [LoginController::class, 'redirect']);
Route::get('/google-auth/callback', [LoginController::class, 'callback']);

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

Route::get('/checkout/{price}', [StripePaymentController::class, 'getMoney'])->middleware('validate.guest.permissions');

//ACCESO PERMITIDO SOLO A ADMINISTRADORES
Route::prefix('admin')->controller(AdminController::class)->middleware('validate.admin.permissions')->group(function () {

    Route::get('/'          ,'getAll');
    Route::get('/{id}'      ,'getOne')->middleware('validate.id');
    Route::post('/'         ,'create');
    Route::put('/'          ,'modify');
    Route::delete('/{id}',  'delete')->middleware('validate.id');

});

Route::prefix('host')->controller(HostController::class)->group(function () {

    //ACCESO PERMITIDO SOLO A HOSTS
    Route::put('/'                  ,'modify')->middleware('validate.host.permissions');
    Route::delete('/'               ,'delete')->middleware('validate.host.permissions');
    Route::get('/bookings'          ,'getBookings')->middleware('validate.host.permissions');
    Route::get('/houses'            ,'getHouses')->middleware('validate.host.permissions');

    //ACCESO PERMITIDO SOLO A ADMINISTRADORES
    Route::get('/'                  ,'getAll')->middleware('validate.admin.permissions');
    Route::get('/{id}'              ,'getOne')->middleware('validate.admin.permissions')->middleware('validate.id');
    Route::put('/{id}'              ,'modify')->middleware('validate.admin.permissions');
    Route::delete('/{id}'           ,'delete')->middleware('validate.admin.permissions')->middleware('validate.id');
    Route::get('/{id}/bookings'     ,'getBookings')->middleware('validate.admin.permissions')->middleware('validate.id');
    Route::get('/{id}/houses'       ,'getHouses')->middleware('validate.admin.permissions')->middleware('validate.id');
    
    //ACCESO PERMITIDO A INVITADOS
    Route::post('/'                 ,'create');
});

Route::prefix('guest')->controller(GuestController::class)->group(function () {

    //ACCESO PERMITIDO SOLO A GUESTS 
    Route::get('/bookings'          ,'getBookings')->middleware('validate.guest.permissions');
    Route::get('/favorites'         ,'getFavorites')->middleware('validate.guest.permissions');
    Route::put('/'                  ,'modify')->middleware('validate.guest.permissions');
    Route::delete('/'               ,'delete')->middleware('validate.guest.permissions');

    //ACCESO PERMITIDO SOLO A ADMINISTRADORES
    Route::get('/'                  ,'getAll')->middleware('validate.admin.permissions');
    Route::get('/{id}'              ,'getOne')->middleware('validate.admin.permissions')->middleware('validate.id');
    Route::put('/{id}'              ,'modify')->middleware('validate.admin.permissions');
    Route::get('/{id}/bookings'     ,'getBookings')->middleware('validate.admin.permissions')->middleware('validate.id');
    Route::get('/{id}/favorites'    ,'getFavorites')->middleware('validate.admin.permissions')->middleware('validate.id');
    Route::put('/{id}'              ,'modify')->middleware('validate.admin.permissions');
    Route::delete('/{id}'           ,'delete')->middleware('validate.admin.permissions');

    //ACCESO PERMITIDO A INVITADOS
    Route::post('/'                 ,'create');
});

Route::prefix('house')->controller(HouseController::class)->group(function () {

    //ACCESO HOST
    Route::post('/'         ,'create')->middleware('validate.host.permissions');
    Route::post('/img'      ,'storeImg')->middleware('validate.host.permissions');

    //ACCESO MIXTO ADMIN Y HOST 
    Route::put('/{id}'      ,'modify')->middleware('validate.login');
    Route::delete('/{id}'   ,'delete')->middleware('validate.login')->middleware('validate.id');

    //ACCESO PERMITIDO A TODOS
    Route::get('/'          ,'getAll');
    Route::get('/{id}'      ,'getOne')->middleware('validate.id');
});

Route::prefix('booking')->controller(BookingController::class)->group(function () {

    //ACCESO PERMITIDO SOLO A ADMINISTRADORES
    Route::get('/'          ,'getAll')->middleware('validate.admin.permissions');
    Route::get('/{id}'      ,'getOne')->middleware('validate.admin.permissions')->middleware('validate.id');
    Route::delete('/{id}'   ,'delete')->middleware('validate.admin.permissions')->middleware('validate.id');

    
    Route::post('/{id}'     ,'create')->middleware('validate.guest.permissions')->middleware('validate.id');
});

Route::prefix('favorite')->controller(FavoriteController::class)->group(function () {

    //ACCESO PERMITIDO SOLO A ADMINS
    Route::get('/'          ,'getAll')->middleware('validate.admin.permissions');
    
    //ACCESO PARA GUEST
    Route::delete('/{id}'   ,'delete')->middleware('validate.guest.permissions')->middleware('validate.id');
    Route::post('/'         ,'create')->middleware('validate.guest.permissions');
});


// RUTAS GENERALES DE AUTENTICACIÓN 
Route::get('/logout', [LoginController::class,'killToken'])->middleware('validate.login');
Route::post('/login', [LoginController::class,'login']);

Route::get('/profile',[LoginController::class,'identify'])->middleware('validate.login');
Route::get('/search/{input}',[HouseController::class,'search']);

Route::post('/checkout',[StripePaymentController::class,'stripeGuestPay']);
Route::post('/getMoney',[StripePaymentController::class,'stripeHostGetMoney']);
