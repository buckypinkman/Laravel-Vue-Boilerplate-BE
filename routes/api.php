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
Route::get('/test', [AuthController::class, 'test']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/logout', [AuthController::class, 'logout']);

    Route::get('users/get_roles', [UserController::class, 'getRoles']);
    Route::apiResource('users', 'App\Http\Controllers\Api\UserController');

    Route::get('roles/get_permissions', [RoleController::class, 'getPermissions']);
    Route::apiResource('roles', 'App\Http\Controllers\Api\RoleController');

    Route::get('agents/list', ['App\Http\Controllers\Api\AgentController', 'list']);
    Route::apiResource('agents', 'App\Http\Controllers\Api\AgentController');

    Route::get('branches/list', ['App\Http\Controllers\Api\BranchController', 'list']);
    Route::apiResource('branches', 'App\Http\Controllers\Api\BranchController');

    Route::get('saving-account-categories/list', ['App\Http\Controllers\Api\SavingAccountCategoryController', 'list']);
    Route::apiResource('saving-account-categories', 'App\Http\Controllers\Api\SavingAccountCategoryController');

    Route::apiResource('saving-account', 'App\Http\Controllers\Api\SavingAccountController');

});
