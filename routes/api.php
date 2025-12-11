<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WordController;
use App\Http\Controllers\ApiSptController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ApiCostController;
use App\Http\Controllers\ApiSppdController;
use App\Http\Controllers\ApiUserController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ApiEmployeeController;
use App\Http\Controllers\ApiDepartureController;
use App\Http\Controllers\ApiReturnTripController;
use App\Http\Controllers\ApiUserProfileController;
use App\Http\Controllers\ApiHeadDivisionController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);

Route::get('/employees/dropdown', [ApiEmployeeController::class, 'listForDropdown']);

Route::middleware('auth:sanctum')->group(function() {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // user
    Route::get('/profile', [ApiUserProfileController::class, 'show']);
    Route::post('/profile', [ApiUserProfileController::class, 'update']);

    Route::post('/user/photo', [AuthController::class, 'updatePhoto']);

    // admin
    Route::get('/user-profiles', [ApiUserProfileController::class, 'index']);
    Route::get('/user-profiles/{id}', [ApiUserProfileController::class, 'showById']);
    Route::post('/user-profiles/{id}', [ApiUserProfileController::class, 'update']);

    Route::put('/password', [ApiUserController::class, 'updatePass']);

    Route::get('/dashboard-stats', [DashboardController::class, 'stats']);

    Route::get('/users', [ApiUserController::class, 'indexUser']);
    Route::get('/users/{id}', [ApiUserController::class, 'showUser']);
    Route::post('/users', [ApiUserController::class, 'storeUser']);
    Route::put('/users/{id}', [ApiUserController::class, 'updateUser']);
    Route::delete('/users/{id}', [ApiUserController::class, 'destroyUser']);

    Route::get('/employees', [ApiEmployeeController::class, 'indexEmployee']);
    Route::get('/employees/available', [ApiEmployeeController::class, 'availableUser']);
    Route::get('/employees/{id}', [ApiEmployeeController::class, 'showEmployee']);
    Route::post('/employees', [ApiEmployeeController::class, 'storeEmployee']);
    Route::put('/employees/{id}', [ApiEmployeeController::class, 'updateEmployee']);
    Route::delete('/employees/{id}', [ApiEmployeeController::class, 'destroyEmployee']);

    Route::get('/head_divisions', [ApiHeadDivisionController::class, 'indexHeadDivision']);
    Route::get('/head_divisions/{id}', [ApiHeadDivisionController::class, 'showHeadDivision']);
    Route::post('/head_divisions', [ApiHeadDivisionController::class, 'storeHeadDivision']);
    Route::put('/head_divisions/{id}', [ApiHeadDivisionController::class, 'updateHeadDivision']);
    Route::delete('/head_divisions/{id}', [ApiHeadDivisionController::class, 'destroyHeadDivision']);

    Route::get('/spts', [ApiSptController::class, 'indexSpt']);
    Route::get('/spts/{id}', [ApiSptController::class, 'showSpt']);
    Route::post('/spts', [ApiSptController::class, 'storeSpt']);
    Route::put('/spts/{id}', [ApiSptController::class, 'updateSpt']);
    Route::delete('/spts/{id}', [ApiSptController::class, 'destroySpt']);

    Route::get('/sppds', [ApiSppdController::class, 'indexSppd']);
    Route::get('/sppds/{id}', [ApiSppdController::class, 'showSppd']);
    Route::put('/sppds/{id}', [ApiSppdController::class, 'updateSppd']);

    Route::get('/costs', [ApiCostController::class, 'indexCost']);
    Route::get('/costs/{id}', [ApiCostController::class, 'showCost']);
    Route::put('/costs/{id}', [ApiCostController::class, 'updateCost']);

    Route::get('/departures', [ApiDepartureController::class, 'indexDeparture']);
    Route::get('/departures/{id}', [ApiDepartureController::class, 'showDeparture']);
    Route::put('/departures/{id}', [ApiDepartureController::class, 'updateDeparture']);

    Route::get('/returns', [ApiReturnTripController::class, 'indexReturnTrip']);
    Route::get('/returns/{id}', [ApiReturnTripController::class, 'showReturnTrip']);
    Route::put('/returns/{id}', [ApiReturnTripController::class, 'updateReturnTrip']);

    Route::post('/templates', [TemplateController::class, 'store']);
    Route::get('/templates', [TemplateController::class, 'index']); 

    Route::get('/years', [ExportController::class, 'years']);
    Route::get('/export/full-rekap', [ExportController::class, 'fullRekap']);

    Route::get('/spts/{id}/cetak', [WordController::class, 'cetakSpt']);
});