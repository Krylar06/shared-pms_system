@extends('admin.layouts.app')

@section('title', 'Device Manager')
@section('page_title', 'Device Manager')

@section('content')
@php
    // Change this if your type name is different, e.g. 'DESKTOP' or 'desktop pc'
    $desktopType = $types->first(fn($t) => strtolower($t->name) === 'desktop');
    $desktopTypeId = $desktopType?->id ?? 0;
@endphp

<div
    x-data="{
        addOpen: false,
        editOpen: false,
        deleteOpen: false,

        desktopTypeId: {{ (int) $desktopTypeId }},
        addTypeId: '{{ $types->first()?->id }}',

        editDevice: {
            id: null,
            device_type_id: '',
            property_number: '',
            brand: '',
            mac_address: '',
            unit_price: '',
            date_acquired: '',
            status: 'available',
            notes: '',
            specs: { motherboard: '', memory: '', hard_disk: '', dvd_drive: '' }
        },

        deleteDeviceId: null,

        isDesktop(typeId) {
            return parseInt(typeId || 0) === parseInt(this.desktopTypeId || 0);
        },

        openEdit(device) {
            device.specs = device.specs ?? {};
            device.specs.motherboard = device.specs.motherboard ?? '';
            device.specs.memory = device.specs.memory ?? '';
            device.specs.hard_disk = device.specs.hard_disk ?? '';
            device.specs.dvd_drive = device.specs.dvd_drive ?? '';

            this.editDevice = device;
            this.editOpen = true;
        },

        openDelete(id) {
            this.deleteDeviceId = id;
            this.deleteOpen = true;
        },
    }"
    class="space-y-4"
>

    {{-- Top bar --}}
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">Device Manager</h1>

        <button type="button"
                class="px-4 py-2 rounded bg-gray-900 text-white"
                @click="addOpen = true">
            + Add Device
        </button>
    </div>

    {{-- Search + Type filter --}}
    <div class="bg-white p-4 rounded shadow-sm">
        <form method="GET" class="flex flex-col md:flex-row gap-2 md:items-center">
            <div class="md:w-64">
                <select name="type" class="w-full border rounded px-3 py-2">
                    <option value="">All device types</option>
                    @foreach($types as $t)
                        <option value="{{ $t->id }}" @selected((int)($typeId ?? 0) === $t->id)>
                            {{ $t->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <input name="q"
                   value="{{ $q }}"
                   placeholder="Search property #, brand, mac..."
                   class="flex-1 border rounded px-3 py-2">

            <div class="flex gap-2">
                <button class="px-4 py-2 rounded bg-blue-600 text-white">Search</button>
                <a href="{{ route('admin.devices.index') }}" class="px-4 py-2 rounded bg-gray-100">Reset</a>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded shadow-sm overflow-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-left">
                <tr>
                    <th class="p-2 border">Type</th>
                    <th class="p-2 border">Property #</th>
                    <th class="p-2 border">Brand</th>
                    <th class="p-2 border">MAC</th>
                    <th class="p-2 border">Acquired</th>
                    <th class="p-2 border">Status</th>
                    <th class="p-2 border">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($devices as $d)
                    <tr>
                        <td class="p-2 border">{{ $d->type?->name ?? '-' }}</td>
                        <td class="p-2 border">{{ $d->property_number }}</td>
                        <td class="p-2 border">{{ $d->brand ?? '-' }}</td>
                        <td class="p-2 border">{{ $d->mac_address ?? '-' }}</td>
                        <td class="p-2 border">{{ $d->date_acquired ?? '-' }}</td>
                        <td class="p-2 border">{{ $d->status }}</td>

                        <td class="p-2 border whitespace-nowrap space-x-2">
                            <a href="{{ route('admin.devices.show', $d) }}"
                                    class="px-3 py-1 rounded bg-green-600 text-white">
                                    View
                            </a>
                            <button type="button"
                                    class="px-3 py-1 rounded bg-gray-900 text-white"
                                    @click="openEdit({
                                        id: {{ $d->id }},
                                        device_type_id: {{ $d->device_type_id }},
                                        property_number: @js($d->property_number),
                                        brand: @js($d->brand ?? ''),
                                        mac_address: @js($d->mac_address ?? ''),
                                        unit_price: @js($d->unit_price ?? ''),
                                        date_acquired: @js($d->date_acquired ?? ''),
                                        status: @js($d->status),
                                        notes: @js($d->notes ?? ''),
                                        specs: @js($d->specs ?? [])
                                    })">
                                Edit
                            </button>

                            <button type="button"
                                    class="px-3 py-1 rounded bg-red-600 text-white"
                                    @click="openDelete({{ $d->id }})">
                                Delete
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="p-4 text-center text-gray-600">No devices found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $devices->links() }}
    </div>

    {{-- ADD MODAL --}}
    <x-modal show="addOpen" title="Add Device">
        <form method="POST" action="{{ route('admin.devices.store') }}" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="text-sm font-medium">Device Type</label>
                    <select name="device_type_id"
                            class="mt-1 w-full border rounded px-3 py-2"
                            required
                            x-model="addTypeId">
                        @foreach($types as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-sm font-medium">Property Number</label>
                    <input name="property_number" class="mt-1 w-full border rounded px-3 py-2" required>
                </div>

                <div>
                    <label class="text-sm font-medium">Brand</label>
                    <input name="brand" class="mt-1 w-full border rounded px-3 py-2">
                </div>

                <div>
                    <label class="text-sm font-medium">MAC Address</label>
                    <input name="mac_address" class="mt-1 w-full border rounded px-3 py-2" placeholder="00:1A:2B:3C:4D:5E">
                </div>

                <div>
                    <label class="text-sm font-medium">Unit Price</label>
                    <input name="unit_price" type="number" step="0.01" class="mt-1 w-full border rounded px-3 py-2">
                </div>

                <div>
                    <label class="text-sm font-medium">Date Acquired</label>
                    <input name="date_acquired" type="date" class="mt-1 w-full border rounded px-3 py-2">
                </div>

                <div>
                    <label class="text-sm font-medium">Status</label>
                    <select name="status" class="mt-1 w-full border rounded px-3 py-2" required>
                        <option value="available">Available</option>
                        <option value="issued">Issued</option>
                        <option value="repair">Repair</option>
                        <option value="retired">Retired</option>
                    </select>
                </div>
            </div>

            {{-- Desktop-only fields --}}
            <div x-show="isDesktop(addTypeId)" class="border rounded p-3 bg-gray-50">
                <div class="font-medium mb-2">Desktop Components</div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="text-sm font-medium">Motherboard</label>
                        <input name="specs[motherboard]" class="mt-1 w-full border rounded px-3 py-2">
                    </div>

                    <div>
                        <label class="text-sm font-medium">Memory (RAM)</label>
                        <input name="specs[memory]" class="mt-1 w-full border rounded px-3 py-2" placeholder="e.g. 8GB DDR4">
                    </div>

                    <div>
                        <label class="text-sm font-medium">Hard Disk</label>
                        <input name="specs[hard_disk]" class="mt-1 w-full border rounded px-3 py-2" placeholder="e.g. 1TB HDD / 256GB SSD">
                    </div>

                    <div>
                        <label class="text-sm font-medium">DVD Drive</label>
                        <input name="specs[dvd_drive]" class="mt-1 w-full border rounded px-3 py-2" placeholder="Yes/No or model">
                    </div>
                </div>
            </div>

            <div>
                <label class="text-sm font-medium">Notes</label>
                <textarea name="notes" rows="3" class="mt-1 w-full border rounded px-3 py-2"></textarea>
            </div>

            <div class="flex gap-2 pt-2">
                <button class="px-4 py-2 rounded bg-blue-600 text-white">Save</button>
                <button type="button" class="px-4 py-2 rounded bg-gray-100" @click="addOpen = false">Cancel</button>
            </div>
        </form>
    </x-modal>

    {{-- EDIT MODAL --}}
    <x-modal show="editOpen" title="Edit Device">
        <form method="POST"
              :action="`{{ url('/admin/devices') }}/${editDevice.id}`"
              class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="text-sm font-medium">Device Type</label>
                    <select name="device_type_id"
                            class="mt-1 w-full border rounded px-3 py-2"
                            required
                            x-model="editDevice.device_type_id">
                        @foreach($types as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-sm font-medium">Property Number</label>
                    <input name="property_number" class="mt-1 w-full border rounded px-3 py-2"
                           x-model="editDevice.property_number" required>
                </div>

                <div>
                    <label class="text-sm font-medium">Brand</label>
                    <input name="brand" class="mt-1 w-full border rounded px-3 py-2"
                           x-model="editDevice.brand">
                </div>

                <div>
                    <label class="text-sm font-medium">MAC Address</label>
                    <input name="mac_address" class="mt-1 w-full border rounded px-3 py-2"
                           x-model="editDevice.mac_address">
                </div>

                <div>
                    <label class="text-sm font-medium">Unit Price</label>
                    <input name="unit_price" type="number" step="0.01"
                           class="mt-1 w-full border rounded px-3 py-2"
                           x-model="editDevice.unit_price">
                </div>

                <div>
                    <label class="text-sm font-medium">Date Acquired</label>
                    <input name="date_acquired" type="date"
                           class="mt-1 w-full border rounded px-3 py-2"
                           x-model="editDevice.date_acquired">
                </div>

                <div>
                    <label class="text-sm font-medium">Status</label>
                    <select name="status" class="mt-1 w-full border rounded px-3 py-2" required
                            x-model="editDevice.status">
                        <option value="available">Available</option>
                        <option value="issued">Issued</option>
                        <option value="repair">Repair</option>
                        <option value="retired">Retired</option>
                    </select>
                </div>
            </div>

            {{-- Desktop-only fields --}}
            <div x-show="isDesktop(editDevice.device_type_id)" class="border rounded p-3 bg-gray-50">
                <div class="font-medium mb-2">Desktop Components</div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="text-sm font-medium">Motherboard</label>
                        <input name="specs[motherboard]" class="mt-1 w-full border rounded px-3 py-2"
                               x-model="editDevice.specs.motherboard">
                    </div>

                    <div>
                        <label class="text-sm font-medium">Memory (RAM)</label>
                        <input name="specs[memory]" class="mt-1 w-full border rounded px-3 py-2"
                               x-model="editDevice.specs.memory">
                    </div>

                    <div>
                        <label class="text-sm font-medium">Hard Disk</label>
                        <input name="specs[hard_disk]" class="mt-1 w-full border rounded px-3 py-2"
                               x-model="editDevice.specs.hard_disk">
                    </div>

                    <div>
                        <label class="text-sm font-medium">DVD Drive</label>
                        <input name="specs[dvd_drive]" class="mt-1 w-full border rounded px-3 py-2"
                               x-model="editDevice.specs.dvd_drive">
                    </div>
                </div>
            </div>

            <div>
                <label class="text-sm font-medium">Notes</label>
                <textarea name="notes" rows="3" class="mt-1 w-full border rounded px-3 py-2"
                          x-model="editDevice.notes"></textarea>
            </div>

            <div class="flex gap-2 pt-2">
                <button class="px-4 py-2 rounded bg-blue-600 text-white">Save Changes</button>
                <button type="button" class="px-4 py-2 rounded bg-gray-100" @click="editOpen = false">Cancel</button>
            </div>
        </form>
    </x-modal>

    {{-- DELETE MODAL --}}
    <x-modal show="deleteOpen" title="Delete Device">
        <div class="space-y-3">
            <div class="text-sm text-gray-700">
                Are you sure you want to delete this device?
            </div>

            <form method="POST" :action="`{{ url('/admin/devices') }}/${deleteDeviceId}`" class="flex gap-2">
                @csrf
                @method('DELETE')

                <button class="px-4 py-2 rounded bg-red-600 text-white">Yes, Delete</button>
                <button type="button" class="px-4 py-2 rounded bg-gray-100" @click="deleteOpen = false">Cancel</button>
            </form>
        </div>
    </x-modal>

</div>
@endsection