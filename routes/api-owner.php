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

///#################################################

Route::post('owner/login',[OwnerController::class,'login']);
Route::group(['middleware'=>['auth:owner']],
    function(){
        Route::get('owner/refresh',[OwnerController::class,'refreshToken']);
        Route::post('owner/add/admin',[OwnerController::class,'addAdmin']);
        Route::get('owner/remove/admin/{id}',[OwnerController::class,'removeAdmin']);
        Route::post('owner/add/project',[ProjectController::class,'addProject']);
        Route::post('owner/update/project/{id}',[ProjectController::class,'updateProject']);
        Route::get('owner/remove/project/{id}',[ProjectController::class,'removeProject']);
        Route::get('owner/show/all-admins',[OwnerController::class,'getAllAdmins']);
        Route::get('owner/show/all-users',[OwnerController::class,'getAllUsers']);
        Route::get('owner/logout',[OwnerController::class,'logout']);

});