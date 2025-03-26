<?php

namespace App\Services;

use App\Http\Requests\JobRequest;
use App\Http\Resources\JobResource;
use App\Models\Job;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class JobService
{
    /**
     * Store a newly created job.
     */
    public function store(JobRequest $request)
    {
        try {
            DB::beginTransaction();
            $validated = $request->validated();
            $job = Job::create($validated);

            $this->handleRelationships($request, $job);

            DB::commit();

            return new JobResource($job->load([
                'languages',
                'categories',
                'attributeValues.attribute',
                'locations'
            ]));
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Job creation failed: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
        $jobsTable= (new Job())->getTable(); // This will get the table name
        $jobs = DB::table($jobsTable)->get();
        return $jobs;
    }

    /**
     * Update the specified job.
     */
    public function update(JobRequest $request, Job $job)
    {
        try {
            DB::beginTransaction();

            // Update main job attributes
            $job->update($request->validated());

            // Handle relationships
            $this->handleRelationships($request, $job);

            DB::commit();

            return new JobResource($job->load([
                'languages',
                'categories',
                'attributeValues.attribute',
                'locations'
            ]));

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Job update failed: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    /**
     * Remove the specified job.
     */
    public function destroy(Job $job)
    {
        try {
            $job->delete();
            return response()->json(null, Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Job deletion failed: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
