<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobAttributeValue extends Model
{
    use HasFactory;
    protected $fillable = [
        'attribute_id',
        'value',
        'job_id' // Add this if you're setting job_id via mass assignment
    ];

    public function getValueAttribute($value)
    {
        switch ($this->attribute->type) {
            case 'number':
                return (float)$value;
            case 'boolean':
                return (bool)$value;
            case 'date':
                return \Carbon\Carbon::parse($value);
            case 'select':
                return $value;
            default: // text
                return (string)$value;
        }
    }

    public function setValueAttribute($value)
    {
        switch ($this->attribute->type) {
            case 'boolean':
                $this->attributes['value'] = $value ? '1' : '0';
                break;
            default:
                $this->attributes['value'] = (string)$value;
        }
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }
}
