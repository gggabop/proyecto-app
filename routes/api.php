<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\CashOrderController;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\API\CEOControlller;
use App\Http\Controllers\DiaryController;
use App\Http\Controllers\LoansController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\Users;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| RUTAS DEL LA API CON MIDDLEWARE DE AUTENTICACION Y ROLES DE USUARIO
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::group([
    'middleware' => 'auth:api'
  ], function() {
      Route::get('logout', [AuthController::class, 'logout']);
      Route::resource('/users', Users::class)->middleware('role:prestamista|admin|secretaria');
      Route::resource('/ceo', CEOControlller::class)->middleware('role:prestamista|admin|secretaria');
      Route::resource('/customer', CustomersController::class)->middleware('role:admin|prestamista|secretaria');
      Route::resource('/audit', AuditController::class)->middleware('role:admin');
      Route::resource('/cashorder', CashOrderController::class)->middleware('role:admin|secretaria');
      Route::resource('/loans', LoansController::class)->middleware('role:admin|prestamista|secretaria');
      Route::get('/cashorderAdd/{id}',[CashOrderController::class, 'addLoan'])->middleware('role:admin|prestamista|secretaria');
      Route::get('/cashorderDeny/{id}',[CashOrderController::class, 'denyLoan'])->middleware('role:admin|prestamista|secretaria');
      Route::resource('/payments', PaymentsController::class)->middleware('role:admin|prestamista|secretaria');
      Route::resource('/diary', DiaryController::class)->middleware('role:admin|prestamista');
      Route::get('/check',  [AuthController::class, 'check']);
      Route::get('/prueba',function(){
        return 'crear nuevo usuario';
      });
  });
