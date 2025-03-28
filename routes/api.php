<?php

use App\Models\Job;
use App\Services\JobFilterService;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\JobController;

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

Route::prefix('v1')->group(function () {
    Route::get('/test/ss', function (\Illuminate\Http\Request $request) {
        $jobs = Job::query()
            ->where('job_type', 'full-time')
            ->whereHas('languages', function($query) {
                $query->whereIn('name', ['PHP', 'JavaScript']);
            })
            ->whereHas('locations', function($query) {
                $query->where(function($subQuery) {
                    $subQuery->where('is_remote', true)
                        ->orWhere('city', 'LIKE', '%New York%')
                        ->orWhere('country', 'LIKE', '%New York%');
                });
            })
            ->whereHas('attributeValues', function($query) {
                $query->whereHas('attribute', function($subQuery) {
                    $subQuery->where('name', 'years_experience');
                })
                    ->whereRaw('CAST(value AS DECIMAL(10,2)) >= ?', [3]);
            })
            ->with(['languages', 'locations', 'attributeValues.attribute'])
            ->get();
        return $jobs;
    });
    Route::get('/jobs', [JobController::class, 'index']);
    Route::post('/jobs/createJob', [JobController::class, 'store']);
    Route::get('/jobs/getJobs', [JobController::class, 'getJobs']);
    Route::put('/jobs/update/{job}', [JobController::class, 'update']);
    Route::delete('/jobs/delete/{job}', [JobController::class, 'destroy']);








    // Public routes (no authentication required)
    Route::get('/jobs', [JobController::class, 'index'])
        ->name('jobs.index');

    Route::get('/jobs/{job}', [JobController::class, 'show'])
        ->name('jobs.show');


    // Job CRUD
    Route::post('/jobs', [JobController::class, 'store'])
        ->name('jobs.store');

    Route::put('/jobs/{job}', [JobController::class, 'update'])
        ->name('jobs.update');

    Route::delete('/jobs/{job}', [JobController::class, 'destroy'])
        ->name('jobs.destroy');

    // Job Status Management
    Route::post('/jobs/{job}/publish', [JobController::class, 'publish'])
        ->name('jobs.publish');

    Route::post('/jobs/{job}/archive', [JobController::class, 'archive'])
        ->name('jobs.archive');

    // Advanced Filtering Endpoint


});

// Fallback route for API versioning
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'API endpoint not found. Please use /v1/ prefix for current API version.',
        'data' => null
    ], 404);
});
