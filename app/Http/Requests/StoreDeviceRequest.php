<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDeviceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
{
    return [
        'device_type_id' => ['required', 'exists:device_types,id'],
        'property_number' => ['required', 'string', 'max:255', 'unique:devices,property_number'],
        'brand' => ['nullable', 'string', 'max:255'],
        'mac_address' => ['nullable', 'string', 'max:255'],
        'unit_price' => ['nullable', 'numeric', 'min:0'],
        'date_acquired' => ['nullable', 'date'],
        'status' => ['required', 'in:available,issued,repair,retired'],
        'notes' => ['nullable', 'string'],

        // ADD THIS
        'specs' => ['nullable', 'array'],
        'specs.motherboard' => ['nullable', 'string', 'max:255'],
        'specs.memory' => ['nullable', 'string', 'max:255'],
        'specs.hard_disk' => ['nullable', 'string', 'max:255'],
        'specs.dvd_drive' => ['nullable', 'string', 'max:255'],
    ];
}
}