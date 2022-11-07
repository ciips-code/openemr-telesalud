<?php

use App\Http\Controllers\{AuthController, JitsiController, PHPMailerController};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function() {
    Route::get('/userinfo', [AuthController::class, 'infouser']);
    Route::post('/revoke', [AuthController::class, 'revoke']);

    // Enrutamiento para pacientes
    Route::get('patients', [PatientController::class, 'showAllPatients']);
    Route::get('patient/{id}', [PatientController::class, 'showOnePatient']);
    Route::get('patient/{id}/encounters', [PatientController::class, 'showAllEncountersByPatient']);
    Route::get('patient/{id}/encounter/{encounterId}', [PatientController::class, 'showPatientEncounterById']);

    // Enturamiento para videoconsultas
    Route::post('vc/create', [JitsiController::class, 'createVC']);
    Route::post('vc/get-link', [JitsiController::class, 'getVcLink']);

    // Email
    Route::post('mail/send', [PHPMailerController::class, 'composeEmail']);

});