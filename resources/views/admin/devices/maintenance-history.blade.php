@extends('admin.layouts.app')

@section('title', 'Maintenance History')
@section('page_title', 'Maintenance History')

@section('content')
<div class="space-y-5">
    <div class="flex items-start justify-between gap-3">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Maintenance History</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ $device->type?->name ?? 'Device' }} |
                Property #: {{ $device->property_number }}
            </p>
        </div>

        <a
            href="{{ route('admin.devices.index') }}"
            class="rounded-xl bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
        >
            Back
        </a>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden dark:border-gray-700 dark:bg-gray-800">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-left dark:bg-gray-900/40">
                <tr>
                    <th class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Date</th>
                    <th class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Type</th>
                    <th class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Remarks</th>
                    <th class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Checked By</th>
                    <th class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Recorded At</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($records as $record)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                            {{ $record->maintenance_date?->format('M d, Y') }}
                        </td>

                        <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                            {{ $record->maintenance_type }}
                        </td>

                        <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                            {{ $record->remarks ?? '-' }}
                        </td>

                        <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                            {{ $record->checkedBy?->name ?? $record->checkedBy?->email ?? '-' }}
                        </td>

                        <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                            {{ $record->created_at?->format('M d, Y h:i A') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                            No maintenance records yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection