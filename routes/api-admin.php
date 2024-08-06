<?php

use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\OwnerController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\AuthAdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\AdminController;

// Route::post('/Admin/register', [AuthAdminController::class, 'register']);
// Route::post('/Admin/login', [AuthAdminController::class, 'login']);


// Route::middleware(['admin','auth:api'])->group(function () {
//     Route::get('/Admin/dashboard', [AdminController::class, 'dashboard']);
//     // Other Admin routes
// });



// Route::post('register',[UserController::class,'register']);
// Route::post('login',[UserController::class,'login']);

Route::post('admin/login',[AdminController::class,'login']);
Route::group(['middleware'=>['auth:admin']],
function(){
    Route::get('admin/show/all-appointments',[AppointmentController::class,'getAllAppointments']);
    Route::get('admin/show/all-users',[UserController::class,'getAllUsers']);
    Route::post('admin/add/project',[ProjectController::class,'addProject']);
    Route::post('admin/update/project/{id}',[ProjectController::class,'updateProject']);
    Route::get('admin/remove/project/{id}',[ProjectController::class,'removeProject']);
        Route::get('admin/logout',[AdminController::class,'logout']);
});
