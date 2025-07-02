<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;

class Society extends Authenticatable
{
    protected $fillable = [
        'id_card_number',
        'password',
        'name',
        'born_date',
        'gender',
        'address',
        'regional_id',
        'login_tokens',
        'auth_token',
        'last_login',
        'last_logout',
    ];

    protected $hidden = [
        'password',
        'login_tokens',
        'auth_token',
    ];

    protected $casts = [
        'born_date' => 'date',
        'last_login' => 'datetime',
        'last_logout' => 'datetime',
    ];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function regional(): BelongsTo
    {
        return $this->belongsTo(Regional::class);
    }

    public function validations(): HasMany
    {
        return $this->hasMany(Validation::class);
    }

    public function jobApplications(): HasMany
    {
        return $this->hasMany(JobApplySociety::class);
    }

    public function jobApplyPositions(): HasMany
    {
        return $this->hasMany(JobApplyPosition::class);
    }
}
