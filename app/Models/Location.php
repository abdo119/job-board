<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Location extends Model
{
    use HasFactory;
    protected $fillable = ['city', 'state', 'country', 'is_remote'];

    public function jobs(): BelongsToMany
    {
        return $this->belongsToMany(Job::class);
    }

    public function getFullLocationAttribute(): string
    {
        return implode(', ', array_filter([
            $this->city,
            $this->state,
            $this->country
        ]));
    }
}
