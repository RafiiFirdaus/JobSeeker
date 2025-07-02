<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Regional extends Model
{
    protected $fillable = [
        'province',
        'district',
    ];

    public function societies(): HasMany
    {
        return $this->hasMany(Society::class);
    }
}
