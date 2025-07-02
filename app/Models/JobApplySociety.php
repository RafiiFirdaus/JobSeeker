<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobApplySociety extends Model
{
    protected $fillable = [
        'notes',
        'date',
        'society_id',
        'job_vacancy_id',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function society(): BelongsTo
    {
        return $this->belongsTo(Society::class);
    }

    public function jobVacancy(): BelongsTo
    {
        return $this->belongsTo(JobVacancy::class);
    }

    public function jobApplyPositions(): HasMany
    {
        return $this->hasMany(JobApplyPosition::class, 'job_apply_societies_id');
    }
}
