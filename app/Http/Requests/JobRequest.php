<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'company_name' => 'required|string|max:255',
            'salary_min' => 'required|numeric',
            'salary_max' => 'required|numeric|gte:salary_min',
            'is_remote' => 'boolean',
            'job_type' => 'required|in:full-time,part-time,contract',
            'status' => 'in:draft,published',
            'languages' => 'array',
            'languages.*' => 'exists:languages,id',
            'locations' => 'array',
            'locations.*.city' => 'required_with:locations|string',
            'locations.*.state' => 'nullable|string',
            'locations.*.country' => 'required_with:locations|string',
        ];
    }
}
