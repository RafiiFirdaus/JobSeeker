<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobCategory extends Model
{
    protected $fillable = [
        'job_category',
    ];

    public function jobVacancies(): HasMany
    {
        return $this->hasMany(JobVacancy::class);
    }

    public function validations(): HasMany
    {
        return $this->hasMany(Validation::class);
    }
}
