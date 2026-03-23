@extends('admin.layouts.app')

@section('title', 'Issued Devices')
@section('page_title', 'Issued Devices')

@section('content')
<div x-data="{
        issueOpen: false,
        editOpen: false,
        deleteOpen: false,
        viewOpen: false,

        editDevice: {
            id: null,
            property_number: '',
            brand: '',
            mac_address: '',
            unit_price: '',
            date_acquired: '',
            status: 'available',
            notes: '',
            specs: {}
        },

        viewDevice: {
            id: null,
            type_name: '',
            property_number: '',
            brand: '',
            mac_address: '',
            unit_price: '',
            date_acquired: '',
            status: '',
            notes: '',
            specs: {}
        },

        deleteDeviceId: null,

        openView(device) {
            device.specs = device.specs ?? {};
            device.specs.motherboard = device.specs.motherboard ?? '';
            device.specs.memory = device.specs.memory ?? '';
            device.specs.hard_disk = device.specs.hard_disk ?? '';
            device.specs.dvd_drive = device.specs.dvd_drive ?? '';
            this.viewDevice = device;
            this.viewOpen = true;
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
        }
    }"
    class="space-y-4"
>

    {{-- Breadcrumb inside content --}}
    <div class="text-sm text-gray-500 leading-6 break-words">
        <a class="text-blue-600 hover:underline" href="{{ route('admin.colleges.index') }}">Colleges</a>
        <span class="mx-1">/</span>
        <a class="text-blue-600 hover:underline" href="{{ route('admin.offices.index', $staff->office->college) }}">
            {{ $staff->office->college->name }}
        </a>
        <span class="mx-1">/</span>
        <a class="text-blue-600 hover:underline" href="{{ route('admin.staff.index', $staff->office) }}">
            {{ $staff->office->name }}
        </a>
        <span class="mx-1">/</span>
        <span class="text-gray-700 font-medium">{{ $staff->last_name }}, {{ $staff->first_name }}</span>
        <span class="mx-1">/</span>
        <span>Issued Devices</span>
    </div>

    <div class="flex items-start justify-between gap-3">
        <h1 class="text-2xl font-semibold">Issued Devices</h1>

        <button
            type="button"
            class="shrink-0 px-4 py-2 rounded-xl bg-blue-600 text-white"
            @click="issueOpen = true"
        >
            + Issue Device
        </button>
    </div>

    {{-- Issue modal --}}
    <x-modal show="issueOpen" title="Issue a device to {{ $staff->first_name }}">
        <form method="POST" action="{{ route('admin.staff.devices.issue', $staff) }}" class="space-y-3">
            @csrf

            <div>
                <label class="text-sm font-medium">Available Devices</label>
                <select name="device_id" class="mt-1 w-full border rounded px-3 py-2" required>
                    <option value="">Select device...</option>
                    @foreach($availableDevices as $d)
                        <option value="{{ $d->id }}">
                            {{ $d->type?->name ?? 'Device' }} | {{ $d->property_number }}
                            {{ $d->brand ? '| '.$d->brand : '' }}
                        </option>
                    @endforeach
                </select>
                @error('device_id')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="flex gap-2 pt-2">
                <button class="px-4 py-2 rounded bg-blue-600 text-white">Issue</button>
                <button type="button" class="px-4 py-2 rounded bg-gray-100" @click="issueOpen = false">Cancel</button>
            </div>
        </form>
    </x-modal>

    {{-- Mobile cards --}}
    <div class="grid grid-cols-1 gap-3 md:hidden">
        @forelse($issued as $a)
            @php $dev = $a->device; @endphp
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 space-y-3">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="text-sm text-gray-500">Type</div>
                        <div class="font-semibold">{{ $dev?->type?->name ?? '-' }}</div>
                    </div>

                    <div class="text-right">
                        <div class="text-sm text-gray-500">Issued At</div>
                        <div class="text-sm">{{ optional($a->issued_at)->format('Y-m-d H:i') }}</div>
                    </div>
                </div>

                <div>
                    <div class="text-sm text-gray-500">Property Number</div>
                    @if($dev)
                        <button
                            type="button"
                            class="text-blue-700 font-medium hover:underline text-left"
                            @click="openView({
                                id: {{ $dev->id }},
                                type_name: @js($dev->type?->name ?? '-'),
                                property_number: @js($dev->property_number),
                                brand: @js($dev->brand ?? ''),
                                mac_address: @js($dev->mac_address ?? ''),
                                unit_price: @js($dev->unit_price ?? ''),
                                date_acquired: @js($dev->date_acquired ?? ''),
                                status: @js($dev->status ?? ''),
                                notes: @js($dev->notes ?? ''),
                                specs: @js($dev->specs ?? [])
                            })"
                        >
                            {{ $dev->property_number }}
                        </button>
                    @else
                        <div>-</div>
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <div class="text-gray-500">Brand</div>
                        <div>{{ $dev?->brand ?? '-' }}</div>
                    </div>

                    <div>
                        <div class="text-gray-500">MAC</div>
                        <div class="break-all">{{ $dev?->mac_address ?? '-' }}</div>
                    </div>
                </div>

                <div class="flex flex-wrap gap-2 pt-1">
                    <form method="POST"
                          action="{{ route('admin.staff.devices.return', [$staff, $a]) }}"
                          class="inline"
                          onsubmit="return confirm('Return this device?')">
                        @csrf
                        <button class="px-3 py-1.5 rounded-lg bg-gray-900 text-white text-sm">Return</button>
                    </form>

                    @if($dev)
                        <button
                            type="button"
                            class="px-3 py-1.5 rounded-lg bg-blue-600 text-white text-sm"
                            @click="openEdit({
                                id: {{ $dev->id }},
                                property_number: @js($dev->property_number),
                                brand: @js($dev->brand ?? ''),
                                mac_address: @js($dev->mac_address ?? ''),
                                unit_price: @js($dev->unit_price ?? ''),
                                date_acquired: @js($dev->date_acquired ?? ''),
                                status: @js($dev->status ?? ''),
                                notes: @js($dev->notes ?? ''),
                                specs: @js($dev->specs ?? [])
                            })"
                        >
                            Edit
                        </button>

                        <button
                            type="button"
                            class="px-3 py-1.5 rounded-lg bg-red-600 text-white text-sm"
                            @click="openDelete({{ $dev->id }})"
                        >
                            Delete
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-gray-200 p-6 text-center text-gray-500">
                No devices currently issued.
            </div>
        @endforelse
    </div>

    {{-- Desktop table --}}
    <div class="hidden md:block bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-left">
                    <tr>
                        <th class="p-3 border-b">Type</th>
                        <th class="p-3 border-b">Property #</th>
                        <th class="p-3 border-b">Brand</th>
                        <th class="p-3 border-b">MAC</th>
                        <th class="p-3 border-b">Issued At</th>
                        <th class="p-3 border-b">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($issued as $a)
                        @php $dev = $a->device; @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="p-3 border-b">{{ $dev?->type?->name ?? '-' }}</td>

                            <td class="p-3 border-b">
                                @if($dev)
                                    <button
                                        type="button"
                                        class="text-blue-700 hover:underline"
                                        @click="openView({
                                            id: {{ $dev->id }},
                                            type_name: @js($dev->type?->name ?? '-'),
                                            property_number: @js($dev->property_number),
                                            brand: @js($dev->brand ?? ''),
                                            mac_address: @js($dev->mac_address ?? ''),
                                            unit_price: @js($dev->unit_price ?? ''),
                                            date_acquired: @js($dev->date_acquired ?? ''),
                                            status: @js($dev->status ?? ''),
                                            notes: @js($dev->notes ?? ''),
                                            specs: @js($dev->specs ?? [])
                                        })"
                                    >
                                        {{ $dev->property_number }}
                                    </button>
                                @else
                                    -
                                @endif
                            </td>

                            <td class="p-3 border-b">{{ $dev?->brand ?? '-' }}</td>
                            <td class="p-3 border-b">{{ $dev?->mac_address ?? '-' }}</td>
                            <td class="p-3 border-b">{{ optional($a->issued_at)->format('Y-m-d H:i') }}</td>

                            <td class="p-3 border-b whitespace-nowrap space-x-2">
                                <form method="POST"
                                      action="{{ route('admin.staff.devices.return', [$staff, $a]) }}"
                                      class="inline"
                                      onsubmit="return confirm('Return this device?')">
                                    @csrf
                                    <button class="px-3 py-1 rounded-lg bg-gray-900 text-white">Return</button>
                                </form>

                                @if($dev)
                                    <button
                                        type="button"
                                        class="px-3 py-1 rounded-lg bg-blue-600 text-white"
                                        @click="openEdit({
                                            id: {{ $dev->id }},
                                            property_number: @js($dev->property_number),
                                            brand: @js($dev->brand ?? ''),
                                            mac_address: @js($dev->mac_address ?? ''),
                                            unit_price: @js($dev->unit_price ?? ''),
                                            date_acquired: @js($dev->date_acquired ?? ''),
                                            status: @js($dev->status ?? ''),
                                            notes: @js($dev->notes ?? ''),
                                            specs: @js($dev->specs ?? [])
                                        })"
                                    >
                                        Edit
                                    </button>

                                    <button
                                        type="button"
                                        class="px-3 py-1 rounded-lg bg-red-600 text-white"
                                        @click="openDelete({{ $dev->id }})"
                                    >
                                        Delete
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-6 text-center text-gray-500">No devices currently issued.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- View modal --}}
    <x-modal show="viewOpen" title="Device Information">
        <div class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                <div>
                    <div class="text-gray-500">Device Type</div>
                    <div class="font-medium" x-text="viewDevice.type_name || '-'"></div>
                </div>

                <div>
                    <div class="text-gray-500">Property Number</div>
                    <div class="font-medium break-all" x-text="viewDevice.property_number || '-'"></div>
                </div>

                <div>
                    <div class="text-gray-500">Brand</div>
                    <div class="font-medium" x-text="viewDevice.brand || '-'"></div>
                </div>

                <div>
                    <div class="text-gray-500">MAC Address</div>
                    <div class="font-medium break-all" x-text="viewDevice.mac_address || '-'"></div>
                </div>

                <div>
                    <div class="text-gray-500">Unit Price</div>
                    <div class="font-medium" x-text="viewDevice.unit_price || '-'"></div>
                </div>

                <div>
                    <div class="text-gray-500">Date Acquired</div>
                    <div class="font-medium" x-text="viewDevice.date_acquired || '-'"></div>
                </div>

                <div>
                    <div class="text-gray-500">Status</div>
                    <div class="font-medium" x-text="viewDevice.status || '-'"></div>
                </div>
            </div>

            <div
                x-show="viewDevice.specs && (viewDevice.specs.motherboard || viewDevice.specs.memory || viewDevice.specs.hard_disk || viewDevice.specs.dvd_drive)"
                class="border rounded p-3 bg-gray-50"
            >
                <div class="font-medium mb-2">Desktop Components</div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                    <div>
                        <div class="text-gray-500">Motherboard</div>
                        <div class="font-medium" x-text="viewDevice.specs?.motherboard || '-'"></div>
                    </div>

                    <div>
                        <div class="text-gray-500">Memory</div>
                        <div class="font-medium" x-text="viewDevice.specs?.memory || '-'"></div>
                    </div>

                    <div>
                        <div class="text-gray-500">Hard Disk</div>
                        <div class="font-medium" x-text="viewDevice.specs?.hard_disk || '-'"></div>
                    </div>

                    <div>
                        <div class="text-gray-500">DVD Drive</div>
                        <div class="font-medium" x-text="viewDevice.specs?.dvd_drive || '-'"></div>
                    </div>
                </div>
            </div>

            <div>
                <div class="text-gray-500 text-sm">Notes</div>
                <div class="font-medium text-sm whitespace-pre-line" x-text="viewDevice.notes || '-'"></div>
            </div>

            <div class="flex flex-wrap gap-2 pt-2">
                <button
                    type="button"
                    class="px-4 py-2 rounded bg-blue-600 text-white"
                    @click="viewOpen = false; openEdit(JSON.parse(JSON.stringify(viewDevice)))"
                >
                    Edit
                </button>

                <a
                    :href="`{{ url('/admin/devices') }}/${viewDevice.id}`"
                    class="px-4 py-2 rounded bg-green-600 text-white"
                >
                    Open Full Page
                </a>

                <button
                    type="button"
                    class="px-4 py-2 rounded bg-gray-100"
                    @click="viewOpen = false"
                >
                    Close
                </button>
            </div>
        </div>
    </x-modal>

    {{-- Edit modal --}}
    <x-modal show="editOpen" title="Edit Device">
        <form method="POST"
              :action="`{{ url('/admin/devices') }}/${editDevice.id}/quick`"
              class="space-y-3">
            @csrf
            @method('PUT')

            <div>
                <label class="text-sm font-medium">Property Number</label>
                <input name="property_number" class="mt-1 w-full border rounded px-3 py-2"
                       x-model="editDevice.property_number" required>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
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
            </div>

            <div
                class="border rounded p-3 bg-gray-50"
                x-show="editDevice.specs && (editDevice.specs.motherboard !== undefined || editDevice.specs.memory !== undefined || editDevice.specs.hard_disk !== undefined || editDevice.specs.dvd_drive !== undefined)"
            >
                <div class="font-medium mb-2">Desktop Components</div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="text-sm font-medium">Motherboard</label>
                        <input name="specs[motherboard]" class="mt-1 w-full border rounded px-3 py-2"
                               x-model="editDevice.specs.motherboard">
                    </div>

                    <div>
                        <label class="text-sm font-medium">Memory</label>
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

    {{-- Delete modal --}}
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