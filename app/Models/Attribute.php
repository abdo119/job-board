<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attribute extends Model
{
    use HasFactory;
    protected $casts = [
        'options' => 'array',
    ];

    public function values(): HasMany
    {
        return $this->hasMany(JobAttributeValue::class);
    }
}
