<?php

use App\Http\Controllers\Api\AppSettingController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DistribusiController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\MonitoringController;
use App\Http\Controllers\Api\QurbanUrutanController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WarehouseController;
use Illuminate\Support\Facades\Route;


Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/logout', [AuthController::class, 'logout']);

    Route::get('users/get_roles', [UserController::class, 'getRoles']);
    Route::apiResource('users', 'App\Http\Controllers\Api\UserController');

    Route::get('roles/get_permissions', [RoleController::class, 'getPermissions']);
    Route::apiResource('roles', 'App\Http\Controllers\Api\RoleController');

    Route::apiResource('agents', 'App\Http\Controllers\Api\AgentController');
});
