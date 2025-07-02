<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Validator extends Model
{
    protected $fillable = [
        'user_id',
        'role',
        'name',
    ];

    public function adminUser(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class, 'user_id');
    }

    public function validations(): HasMany
    {
        return $this->hasMany(Validation::class);
    }
}
