<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDeviceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'device_type_id' => ['required', 'exists:device_types,id'],

            'property_number' => [
                'required',
                'string',
                'max:255',
                'unique:devices,property_number,' . $this->route('device')->id,
            ],

            'serial_number' => [
                'nullable',
                'string',
                'max:255',
            ],

            'brand' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'mac_address' => ['nullable', 'string', 'max:255'],

            'unit_price' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'date_acquired' => ['nullable', 'date'],

            /*
            |--------------------------------------------------------------------------
            | Keep status nullable
            |--------------------------------------------------------------------------
            | The UI does not show Availability anymore, but the hidden field still
            | sends it. Nullable prevents validation issues if the field is missing.
            */
            'status' => ['nullable', 'in:available,issued,repair,retired'],

            /*
            |--------------------------------------------------------------------------
            | Device Condition
            |--------------------------------------------------------------------------
            */
            'condition' => ['nullable', 'in:serviceable,unserviceable'],

            'notes' => ['nullable', 'string'],

            'last_maintenance_date' => ['nullable', 'date'],
            'maintenance_remarks' => ['nullable', 'string'],

            /*
            |--------------------------------------------------------------------------
            | Computer Specs
            |--------------------------------------------------------------------------
            */
            'specs' => ['nullable', 'array'],
            'specs.os' => ['nullable', 'string', 'max:255'],
            'specs.os_version' => ['nullable', 'string', 'max:255'],
            'specs.os_license' => ['nullable', 'string', 'max:255'],
            'specs.memory' => ['nullable', 'string', 'max:255'],
            'specs.storage' => ['nullable', 'string', 'max:255'],
            'specs.form_factor' => ['nullable', 'string', 'max:255'],
            'specs.office_version' => ['nullable', 'string', 'max:255'],
            'specs.office_license' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'unit_price.numeric' => 'The unit price must be a valid number.',
            'unit_price.min' => 'The unit price cannot be negative.',
            'unit_price.max' => 'The unit price is too large. Please enter a valid amount, for example 13000 or 25500.',

            'condition.in' => 'The condition must be either serviceable or unserviceable.',

            'serial_number.max' => 'The serial number must not exceed 255 characters.',

            'specs.os.max' => 'The operating system field must not exceed 255 characters.',
            'specs.os_version.max' => 'The operating system version field must not exceed 255 characters.',
            'specs.os_license.max' => 'The operating system license field must not exceed 255 characters.',
            'specs.memory.max' => 'The memory field must not exceed 255 characters.',
            'specs.storage.max' => 'The storage field must not exceed 255 characters.',
            'specs.form_factor.max' => 'The form factor field must not exceed 255 characters.',
            'specs.office_version.max' => 'The Microsoft Office field must not exceed 255 characters.',
            'specs.office_license.max' => 'The Microsoft Office license field must not exceed 255 characters.',
        ];
    }
}
