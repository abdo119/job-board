<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\JobRequest;
use App\Http\Resources\JobResource;
use App\Models\Job;
use App\Services\JobFilterService;
use App\Services\JobService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class JobController extends Controller
{

    public jobService $jobService;



    /**
     * Display a filtered listing of jobs.
     */
    public function index(Request $request)
    {
        try {
            $params = $request->all();

            return (new JobFilterService())->filter(['filter' => $params['filter']]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Store a newly created job.
     */
    public function store(JobRequest $request)
    {
        $this->jobService = new JobService();
       return $this->jobService->store($request);
    }

    /**
     * Display the specified job.
     */
    public function show(Job $job)
    {
        return new JobResource($job->load(['languages','categories', 'locations', 'attributeValues.attribute']));
    }
    public function getJobs()
    {
        $this->jobService = new JobService();
        return $this->jobService->getJobs();
    }

    /**
     * Update the specified job.
     */
    public function update(JobRequest $request, Job $job)
    {
        $this->jobService = new JobService();
        return $this->jobService->update($request,$job);
    }
    /**
     * Remove the specified job.
     */
    public function destroy($job)
    {
        $this->jobService = new JobService($job);
        return $this->jobService->destroy($job);
    }
    protected function handleRelationships($request, $job)
    {
        // Sync many-to-many relationships
        if ($request->has('languages')) {
            $job->languages()->sync($request->input('languages'));
        }

        if ($request->has('categories')) {
            $job->categories()->sync($request->input('categories'));
        }

        // Handle attributes (assuming a hasMany relationship)
        if ($request->has('attributes')) {
            $job->attributeValues()->delete();
            $job->attributeValues()->createMany(
                $request->input('attributes')
            );
        }

        // Handle locations (hasMany relationship)
        if ($request->has('locations')) {
            $job->locations()->delete();
            $job->locations()->createMany(
                $request->input('locations')
            );
        }
    }
}
