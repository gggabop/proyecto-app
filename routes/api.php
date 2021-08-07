<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\API\CEOControlller;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\CashOrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomersController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::group([
    'middleware' => 'auth:api'
  ], function() {
      Route::get('logout', [AuthController::class, 'logout']);
      Route::resource('/ceo', CEOControlller::class)->middleware('role:prestamista|admin|secretaria');
      Route::resource('/customer', CustomersController::class)->middleware('role:admin|prestamista|secretaria');
      Route::resource('/audit', AuditController::class)->middleware('role:admin');
      Route::resource('/cashorder', CashOrderController::class)->middleware('role:admin|secretaria');
  });
