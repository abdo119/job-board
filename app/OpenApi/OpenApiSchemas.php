<?php

namespace App\OpenApi;

/**
 * @OA\OpenApi(
 *     @OA\Components(
 *         @OA\Schema(
 *             schema="JobResource",
 *             type="object",
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="title", type="string", example="Senior Developer"),
 *                 @OA\Property(property="description", type="string"),
 *                 @OA\Property(property="company_name", type="string", example="Tech Corp"),
 *                 @OA\Property(property="salary_min", type="number", format="float", example=80000),
 *                 @OA\Property(property="salary_max", type="number", format="float", example=120000),
 *                 @OA\Property(property="is_remote", type="boolean", example=true),
 *                 @OA\Property(
 *                     property="job_type",
 *                     type="string",
 *                     enum={"full-time", "part-time", "contract", "freelance"}
 *                 ),
 *                 @OA\Property(
 *                     property="status",
 *                     type="string",
 *                     enum={"draft", "published", "archived"}
 *                 ),
 *                 @OA\Property(property="published_at", type="string", format="date-time"),
 *                 @OA\Property(
 *                     property="languages",
 *                     type="array",
 *                     @OA\Items(ref="#/components/schemas/Language")
 *                 ),
 *                 @OA\Property(
 *                     property="locations",
 *                     type="array",
 *                     @OA\Items(ref="#/components/schemas/Location")
 *                 ),
 *                 @OA\Property(
 *                     property="categories",
 *                     type="array",
 *                     @OA\Items(ref="#/components/schemas/Category")
 *                 ),
 *                 @OA\Property(
 *                     property="attributes",
 *                     type="object",
 *                     additionalProperties=true,
 *                     example={"years_experience": 5, "certification_required": true}
 *                 )
 *             )
 *         ),
 *         @OA\Schema(
 *             schema="Language",
 *             type="object",
 *             @OA\Property(property="id", type="integer"),
 *             @OA\Property(property="name", type="string", example="PHP")
 *         ),
 *         @OA\Schema(
 *             schema="Location",
 *             type="object",
 *             @OA\Property(property="id", type="integer"),
 *             @OA\Property(property="city", type="string", example="New York"),
 *             @OA\Property(property="state", type="string", example="NY"),
 *             @OA\Property(property="country", type="string", example="US")
 *         ),
 *         @OA\Schema(
 *             schema="Category",
 *             type="object",
 *             @OA\Property(property="id", type="integer"),
 *             @OA\Property(property="name", type="string", example="Backend Development")
 *         )
 *     )
 * )
 */
class OpenApiSchemas {}
