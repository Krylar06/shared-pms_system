<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Office extends Model
{
    protected $fillable = ['college_id', 'name'];

    public function college(): BelongsTo
    {
        return $this->belongsTo(College::class);
    }

    public function staff(): HasMany
    {
        return $this->hasMany(Staff::class);
    }
}