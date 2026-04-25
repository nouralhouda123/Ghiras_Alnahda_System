<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\courseController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\VolunteerRequestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
Route::post('register', [AuthController::class, 'register']);
Route::post('verify', [AuthController::class, 'verify']);
Route::post('login', [\App\Http\Controllers\AuthController::class, 'login'])->middleware('role.throttle');
Route::middleware('auth:sanctum')->group(function () {
    Route::post('create_Campanig', [CampaignController::class, 'create'])->middleware('can:create.campaign');
    Route::post('logout', [UserController::class, 'logout']) ;
    Route::get('show_Campanig', [CampaignController::class, 'show']);
    Route::post('indexDetail_Campanig/{id}', [CampaignController::class, 'indexDetail']);
    Route::get('profile', [UserController::class, 'profile']);
    Route::post('volunteerjoin', [VolunteerRequestController::class, 'store']);
    Route::get('profile', [UserController::class, 'profile']);
    Route::post('profileupdate', [UserController::class, 'updateProfile']);
    Route::get('card', [UserController::class, 'cadr']);
    Route::post('storeDepartment', [DepartmentController::class, 'store']);
    Route::get('showAllDepartment', [DepartmentController::class, 'index']);
    //قسم الادارة
    Route::post('addUser', [UserController::class, 'addUser'])->middleware('can:add.user');
    Route::get('getRoleNames', [UserController::class, 'getRoleNames']);
    Route::get('showAllEmployee', [UserController::class, 'showAllEmployeeCampanig'])
        ->middleware('can:show.Employee');
        Route::post('UpdateEmployee/{id}', [UserController::class, 'UpdateEmployee'])
            ->middleware('can:Update.Employee');
    Route::post('SearchCampaign', [CampaignController::class, 'SearchCampaign']);
        Route::post('ShowdetailEmployee/{id}', [UserController::class, 'ShowdetailEmployee'])
            ->middleware('can:Showdetail.Employee');
//قسم الكورسات
    Route::post('addCourse', [\App\Http\Controllers\courseController::class, 'create'])
        ->middleware('can:add.course');
    Route::get('indexAllCourses', [\App\Http\Controllers\courseController::class, 'index']);
    Route::post('indexDetailCourse/{id}', [\App\Http\Controllers\courseController::class, 'show']);
    Route::post('courses/enroll/{id}', [CourseController::class, 'store']);













    // --- راوتات طلبات التطوع ---

    // 1. تقديم طلب جديد (للمستخدم)
    Route::post('volunteerjoin', [VolunteerRequestController::class, 'store']);

    // 2. عرض جميع الطلبات المعلقة (للأدمن أو من لديه صلاحية)
    Route::get('showAllVolunteerRequests', [VolunteerRequestController::class, 'index']);

    // 3. عرض تفاصيل طلب واحد
    Route::get('showVolunteerRequest/{id}', [VolunteerRequestController::class, 'show']);
    // 4. قبول أو رفض الطلب
    Route::post('updateVolunteerRequestStatus/{id}', [VolunteerRequestController::class, 'updateStatus']);

});
