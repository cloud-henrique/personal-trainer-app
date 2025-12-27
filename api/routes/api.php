<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\MeController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\MeasurementController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\WorkoutController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (v1)
|--------------------------------------------------------------------------
|
| All routes are prefixed with /api/v1 (configured in bootstrap/app.php)
|
*/

// ========================================
// Public Routes (No Authentication)
// ========================================

Route::post('/auth/register', [RegisterController::class, 'store']);
Route::post('/auth/login', [LoginController::class, 'store']);

// ========================================
// Protected Routes (Require Authentication)
// ========================================

Route::middleware('auth:sanctum')->group(function () {

    // Auth Routes
    Route::post('/auth/logout', [LogoutController::class, 'destroy']);
    Route::get('/auth/me', [MeController::class, 'show']);

    // Dashboard Routes
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
    Route::get('/dashboard/recent-activity', [DashboardController::class, 'recentActivity']);

    // Student Routes
    Route::apiResource('students', StudentController::class);

    // Measurements Routes
    Route::get('/students/{student}/measurements', [MeasurementController::class, 'index']);
    Route::post('/students/{student}/measurements', [MeasurementController::class, 'store']);
    Route::get('/students/{student}/measurements/latest', [MeasurementController::class, 'latest']);
    Route::get('/students/{student}/measurements/graph', [MeasurementController::class, 'graph']);

    // Workout Routes
    Route::apiResource('workouts', WorkoutController::class);

    // Exercise Routes (nested under workouts)
    Route::post('/workouts/{workout}/exercises', [ExerciseController::class, 'store']);
    Route::put('/exercises/{exercise}', [ExerciseController::class, 'update']);
    Route::delete('/exercises/{exercise}', [ExerciseController::class, 'destroy']);

    // Goal Routes
    Route::get('/students/{student}/goals', [GoalController::class, 'index']);
    Route::post('/students/{student}/goals', [GoalController::class, 'store']);
    Route::put('/goals/{goal}', [GoalController::class, 'update']);
    Route::delete('/goals/{goal}', [GoalController::class, 'destroy']);
});
