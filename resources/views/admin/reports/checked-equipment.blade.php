@extends('admin.layouts.app')

@section('title', 'Checked Equipment Report')
@section('page_title', 'Checked Equipment Report')
@section('breadcrumbs')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600 dark:hover:text-blue-400">Dashboard</a>
    <span class="dark:text-gray-500">/</span>
    <a href="{{ route('admin.reports.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400">Reports</a>
    <span class="dark:text-gray-500">/</span>
    <span class="font-medium text-gray-800 dark:text-gray-200">Registered Accounts</span>
@endsection

@section('content')
<div class="space-y-5">
    <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Checked Equipment Report</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Equipment marked checked through the maintenance checklist.</p>
        </div>
        <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center rounded-xl bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">Back to Reports</a>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        @forelse(($checkerSummary ?? $adminSummary)->take(3) as $summary)
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $summary->checkedBy?->name ?? 'Unknown User' }}</div>
                <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($summary->total) }}</div>
                <div class="mt-1 text-xs uppercase tracking-wide text-gray-400 dark:text-gray-500">Marked checked</div>
            </div>
        @empty
            <div class="rounded-2xl border border-gray-200 bg-white p-5 text-sm text-gray-500 shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-400 md:col-span-3">
                No checked-equipment records yet.
            </div>
        @endforelse
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
        <form method="GET" class="grid grid-cols-1 gap-3 lg:grid-cols-6">
            <input name="q" value="{{ $q }}" placeholder="Search property #, remarks..." class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500 dark:focus:border-blue-500 dark:focus:ring-blue-900">

            <select name="checker_id" class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:focus:border-blue-500 dark:focus:ring-blue-900">
                <option value="">All checked by</option>
                @foreach(($checkerUsers ?? $adminUsers) as $checker)
                    <option value="{{ $checker->id }}" @selected((int)($checkerId ?? $adminId ?? 0) === $checker->id)>{{ $checker->name }}</option>
                @endforeach
            </select>

            <select name="type_id" class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:focus:border-blue-500 dark:focus:ring-blue-900">
                <option value="">All device types</option>
                @foreach($types as $type)
                    <option value="{{ $type->id }}" @selected((int) $typeId === $type->id)>{{ $type->name }}</option>
                @endforeach
            </select>

            <input type="date" name="date_from" value="{{ $dateFrom }}" class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:[color-scheme:dark] dark:focus:border-blue-500 dark:focus:ring-blue-900">
            <input type="date" name="date_to" value="{{ $dateTo }}" class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:[color-scheme:dark] dark:focus:border-blue-500 dark:focus:ring-blue-900">

            <div class="flex gap-2">
                <button type="submit" class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">Generate</button>
                <a href="{{ route('admin.reports.checkedEquipment') }}" class="rounded-xl bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">Reset</a>
            </div>
        </form>
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
        <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4 dark:border-gray-700">
            <div>
                <h2 class="font-semibold text-gray-900 dark:text-gray-100">Marked Checked Records</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ number_format($records->total()) }} result(s)</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                    <tr>
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Checked By</th>
                        <th class="px-4 py-3">Device</th>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3">Office / College</th>
                        <th class="px-4 py-3">Remarks</th>
                        <th class="px-4 py-3">PDF</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($records as $record)
                        @php
                            $device = $record->device;
                            $assignment = $device?->currentAssignment;
                            $office = $assignment?->staff?->office;
                            $college = $office?->college;
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $record->maintenance_date ? $record->maintenance_date->format('M d, Y') : '-' }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">{{ $record->checkedBy?->name ?? '-' }}</td>
                            <td class="px-4 py-3">
                                @if($device)
                                    <a href="{{ route('admin.devices.show', $device) }}" class="font-medium text-blue-700 hover:underline dark:text-blue-400">{{ $device->property_number }}</a>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">SN: {{ $device->serial_number ?: '-' }}</div>
                                @else
                                    <span class="text-gray-400 dark:text-gray-500">Device deleted</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $device?->type?->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                                {{ $office?->name ?? '-' }}
                                @if($college)
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $college->name }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $record->remarks ?: '-' }}</td>
                            <td class="px-4 py-3">
                                @if($device)
                                    <a
                                        href="{{ route('admin.reports.checkedEquipment.pdf', $record) }}"
                                        target="_blank"
                                        rel="noopener"
                                        class="rounded-lg bg-gray-900 px-3 py-1.5 text-sm font-medium text-white hover:bg-black dark:bg-gray-100 dark:text-gray-900 dark:hover:bg-white"
                                    >
                                        PDF
                                    </a>
                                @else
                                    <span class="text-gray-400 dark:text-gray-500">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">No records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-gray-200 px-5 py-4 dark:border-gray-700">
            {{ $records->links() }}
        </div>
    </div>
</div>
@endsection