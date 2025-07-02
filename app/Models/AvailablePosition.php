<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AvailablePosition extends Model
{
    protected $fillable = [
        'job_vacancy_id',
        'position',
        'capacity',
        'apply_capacity',
    ];

    public function jobVacancy(): BelongsTo
    {
        return $this->belongsTo(JobVacancy::class);
    }

    public function jobApplyPositions(): HasMany
    {
        return $this->hasMany(JobApplyPosition::class, 'position_id');
    }
}
