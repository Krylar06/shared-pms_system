@extends('admin.layouts.app')

@section('title', 'Device Details')
@section('page_title', 'Device Details')

@section('breadcrumb')
    <a class="text-blue-700 hover:underline" href="{{ route('admin.devices.index') }}">Device Manager</a>
    <span class="mx-2">/</span>
    <span>{{ $device->property_number }}</span>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Device information --}}
    <div class="lg:col-span-2 bg-white rounded shadow-sm p-6 space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold">{{ $device->property_number }}</h1>
                <div class="text-sm text-gray-600 mt-1">
                    {{ $device->type?->name ?? '-' }}
                </div>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('admin.devices.edit', $device) }}"
                   class="px-4 py-2 rounded bg-blue-600 text-white">
                    Edit
                </a>

                <form method="POST" action="{{ route('admin.devices.destroy', $device) }}"
                      onsubmit="return confirm('Delete this device?')">
                    @csrf
                    @method('DELETE')
                    <button class="px-4 py-2 rounded bg-red-600 text-white">
                        Delete
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <div class="text-gray-500">Device Type</div>
                <div class="font-medium">{{ $device->type?->name ?? '-' }}</div>
            </div>

            <div>
                <div class="text-gray-500">Property Number</div>
                <div class="font-medium">{{ $device->property_number }}</div>
            </div>

            <div>
                <div class="text-gray-500">Brand</div>
                <div class="font-medium">{{ $device->brand ?? '-' }}</div>
            </div>

            <div>
                <div class="text-gray-500">MAC Address</div>
                <div class="font-medium">{{ $device->mac_address ?? '-' }}</div>
            </div>

            <div>
                <div class="text-gray-500">Unit Price</div>
                <div class="font-medium">
                    {{ $device->unit_price !== null ? number_format((float)$device->unit_price, 2) : '-' }}
                </div>
            </div>

            <div>
                <div class="text-gray-500">Date Acquired</div>
                <div class="font-medium">{{ $device->date_acquired ?? '-' }}</div>
            </div>

            <div>
                <div class="text-gray-500">Status</div>
                <div class="font-medium">{{ ucfirst($device->status) }}</div>
            </div>
        </div>

        {{-- Assignment info --}}
        <div class="border-t pt-4">
            <h2 class="font-semibold mb-3">Current Assignment</h2>

            @if($device->currentAssignment && $device->currentAssignment->staff)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <div class="text-gray-500">Issued To</div>
                        <div class="font-medium">
                            {{ $device->currentAssignment->staff->last_name }},
                            {{ $device->currentAssignment->staff->first_name }}
                        </div>
                    </div>

                    <div>
                        <div class="text-gray-500">Office</div>
                        <div class="font-medium">
                            {{ $device->currentAssignment->staff->office->name ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-gray-500">College</div>
                        <div class="font-medium">
                            {{ $device->currentAssignment->staff->office->college->name ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-gray-500">Issued At</div>
                        <div class="font-medium">
                            {{ optional($device->currentAssignment->issued_at)->format('Y-m-d H:i') }}
                        </div>
                    </div>
                </div>
            @else
                <div class="text-sm text-gray-600">This device is not currently issued.</div>
            @endif
        </div>

        {{-- Desktop specs --}}
        @if(!empty($device->specs))
            <div class="border-t pt-4">
                <h2 class="font-semibold mb-3">Specifications</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    @foreach($device->specs as $key => $value)
                        <div>
                            <div class="text-gray-500">
                                {{ ucfirst(str_replace('_', ' ', $key)) }}
                            </div>
                            <div class="font-medium">{{ $value ?: '-' }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="border-t pt-4">
            <h2 class="font-semibold mb-2">Notes</h2>
            <div class="text-sm whitespace-pre-line">
                {{ $device->notes ?: '-' }}
            </div>
        </div>
    </div>

    {{-- QR code --}}
    <div class="bg-white rounded shadow-sm p-6">
        <h2 class="font-semibold mb-4">QR Code</h2>

        <div class="flex justify-center">
            {!! QrCode::size(220)->generate(route('admin.devices.show', $device)) !!}
        </div>

        <div class="mt-4 text-sm text-gray-600 break-all">
            {{ route('admin.devices.show', $device) }}
        </div>

        <div class="mt-4 text-sm text-gray-700">
            Scan this QR code to open this device page directly.
        </div>

        <div class="mt-4">
            <button onclick="window.print()"
                    class="px-4 py-2 rounded bg-gray-900 text-white">
                Print QR
            </button>
        </div>
    </div>

</div>
@endsection