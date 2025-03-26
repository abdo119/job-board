<?php

namespace App\OpenApi;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Job Board API",
 *     description="API for managing job listings with advanced filtering",
 *     @OA\Contact(
 *         email="support@jobboard.com",
 *         name="API Support Team"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 *
 * @OA\Server(
 *     url="http://localhost:8000/api/v1",
 *     description="Local Development Server"
 * )
 *
 * @OA\Server(
 *     url="https://api.jobboard.com/v1",
 *     description="Production Server"
 * )
 *
 * @OA\Tag(
 *     name="Jobs",
 *     description="Job posting operations"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class OpenApi {}
