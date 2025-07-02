<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Validation extends Model
{
    protected $fillable = [
        'job_category_id',
        'society_id',
        'validator_id',
        'status',
        'work_experience',
        'position',
        'reason_accepted',
        'validator_notes',
    ];

    public function jobCategory(): BelongsTo
    {
        return $this->belongsTo(JobCategory::class);
    }

    public function society(): BelongsTo
    {
        return $this->belongsTo(Society::class);
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(Validator::class);
    }
}
