<?php

namespace App\Models;

use Cassandra\Cluster\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Category;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Make sure this import exists
class Job extends Model
{
    use HasFactory;
    protected $casts = [
        'salary_min' => 'decimal:2',
        'salary_max' => 'decimal:2',
        'is_remote' => 'boolean',
        'published_at' => 'datetime',
    ];
    protected $fillable = [
        'title',
        'description',
        'company_name',
        'salary_min',
        'salary_max',
        'is_remote',
        'job_type',
        'status',
        'published_at',
        // ... any other fields you need to mass assign
    ];

    public function languages(): BelongsToMany
    {
        return $this->belongsToMany(Language::class);
    }

    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(Location::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'job_category');
    }

    public function attributeValues(): HasMany
    {
        return $this->hasMany(JobAttributeValue::class);
    }

    // Scopes for common filters
    public function scopePublished(Builder $query): void
    {
        $query->where('status', 'published')
            ->whereNotNull('published_at');
    }

    public function scopeWithSalaryBetween(Builder $query, $min, $max): void
    {
        $query->where('salary_min', '>=', $min)
            ->where('salary_max', '<=', $max);
    }
}
