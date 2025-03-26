<?php

namespace App\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="StoreJobRequest",
 *     type="object",
 *     required={"title", "company_name", "job_type"},
 *     @OA\Property(property="title", type="string", example="Senior Developer"),
 *     @OA\Property(property="description", type="string", example="Job description here"),
 *     @OA\Property(property="company_name", type="string", example="Tech Corp"),
 *     @OA\Property(property="salary_min", type="number", format="float", example=80000),
 *     @OA\Property(property="salary_max", type="number", format="float", example=120000),
 *     @OA\Property(property="is_remote", type="boolean", example=true),
 *     @OA\Property(
 *         property="job_type",
 *         type="string",
 *         enum={"full-time", "part-time", "contract", "freelance"},
 *         example="full-time"
 *     ),
 *     @OA\Property(
 *         property="languages",
 *         type="array",
 *         @OA\Items(type="integer", example=1)
 *     ),
 *     @OA\Property(
 *         property="locations",
 *         type="array",
 *         @OA\Items(type="integer", example=1)
 *     ),
 *     @OA\Property(
 *         property="categories",
 *         type="array",
 *         @OA\Items(type="integer", example=1)
 *     ),
 *     @OA\Property(
 *         property="attributes",
 *         type="object",
 *         additionalProperties=true,
 *         example={"years_experience": 5, "certification_required": true}
 *     )
 * )
 */
class StoreJobRequestSchema {}
