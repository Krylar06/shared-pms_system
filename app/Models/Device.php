<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Device extends Model
{
  protected $fillable = [
    'device_type_id',
    'property_number',
    'brand',
    'mac_address',
    'unit_price',
    'date_acquired',
    'status',
    'notes',
    'specs',   // VERY IMPORTANT
];

   protected $casts = [
    'specs' => 'array',
    'date_acquired' => 'date',
];
        
    public function type(): BelongsTo
    {
        return $this->belongsTo(DeviceType::class, 'device_type_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(DeviceAssignment::class);
    }

    public function currentAssignment(): HasOne
    {
        return $this->hasOne(DeviceAssignment::class)->whereNull('returned_at');
    }
}