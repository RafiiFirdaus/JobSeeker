<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobApplyPosition extends Model
{
    protected $fillable = [
        'date',
        'society_id',
        'job_vacancy_id',
        'position_id',
        'job_apply_societies_id',
        'status',
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

    public function availablePosition(): BelongsTo
    {
        return $this->belongsTo(AvailablePosition::class, 'position_id');
    }

    public function jobApplySociety(): BelongsTo
    {
        return $this->belongsTo(JobApplySociety::class, 'job_apply_societies_id');
    }
}
