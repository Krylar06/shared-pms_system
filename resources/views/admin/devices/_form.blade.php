@php
    $editing = isset($device);
    $selectedTypeId = old('device_type_id', $editing ? $device->device_type_id : null);
    $selectedType = $types->firstWhere('id', (int) $selectedTypeId);
    $typeSlug = $selectedType?->slug;
    $template = $specTemplates[$typeSlug] ?? [];
    $existingSpecs = old('specs', $editing ? ($device->specs ?? []) : []);
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="text-sm font-medium">Device Type</label>
        <select name="device_type_id" class="mt-1 w-full border rounded px-3 py-2" required onchange="this.form.submit()">
            <option value="">Select type...</option>
            @foreach($types as $t)
                <option value="{{ $t->id }}" @selected((int)$selectedTypeId === $t->id)>
                    {{ $t->name }}
                </option>
            @endforeach
        </select>
        @error('device_type_id') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
        <div class="text-xs text-gray-500 mt-1">Changing type reloads form to show matching specs fields.</div>
    </div>

    <div>
        <label class="text-sm font-medium">Property Number</label>
        <input name="property_number" value="{{ old('property_number', $editing ? $device->property_number : '') }}"
               class="mt-1 w-full border rounded px-3 py-2" required>
        @error('property_number') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="text-sm font-medium">Brand</label>
        <input name="brand" value="{{ old('brand', $editing ? $device->brand : '') }}"
               class="mt-1 w-full border rounded px-3 py-2">
    </div>

    <div>
        <label class="text-sm font-medium">Model</label>
        <input name="model" value="{{ old('model', $editing ? $device->model : '') }}"
               class="mt-1 w-full border rounded px-3 py-2">
    </div>

    <div>
        <label class="text-sm font-medium">Serial Number</label>
        <input name="serial_number" value="{{ old('serial_number', $editing ? $device->serial_number : '') }}"
               class="mt-1 w-full border rounded px-3 py-2">
    </div>

    <div>
        <label class="text-sm font-medium">Unit Price</label>
        <input name="unit_price" type="number" step="0.01"
               value="{{ old('unit_price', $editing ? $device->unit_price : '') }}"
               class="mt-1 w-full border rounded px-3 py-2">
        @error('unit_price') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="text-sm font-medium">Status</label>
        <select name="status" class="mt-1 w-full border rounded px-3 py-2" required>
            @foreach(['available','issued','repair','retired'] as $st)
                <option value="{{ $st }}" @selected(old('status', $editing ? $device->status : 'available') === $st)>
                    {{ ucfirst($st) }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<div class="mt-6">
    <h3 class="font-semibold mb-2">Specifications</h3>

    @if(empty($template))
        <div class="text-sm text-gray-600">
            No template specs for this device type yet. You can add templates in DeviceController::specTemplates().
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($template as $key => $label)
                <div>
                    <label class="text-sm font-medium">{{ $label }}</label>
                    <input name="specs[{{ $key }}]" value="{{ $existingSpecs[$key] ?? '' }}"
                           class="mt-1 w-full border rounded px-3 py-2">
                </div>
            @endforeach
        </div>
    @endif
</div>

<div class="mt-6">
    <label class="text-sm font-medium">Notes</label>
    <textarea name="notes" class="mt-1 w-full border rounded px-3 py-2" rows="3">{{ old('notes', $editing ? $device->notes : '') }}</textarea>
</div>