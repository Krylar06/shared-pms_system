@extends('admin.layouts.app')

@section('title', 'Activity Logs')
@section('page_title', 'Activity Logs')

@section('content')
@php
    $chipClass = fn($action) => match ($action) {
        'created' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
        'updated' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
        'deleted' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
        'issued' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
        'returned' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300',
        default => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200',
    };

    $actionIcon = fn($action) => match ($action) {
        'created' => '➕',
        'updated' => '✏️',
        'deleted' => '🗑️',
        'issued' => '📤',
        'returned' => '📥',
        default => '•',
    };
@endphp

<script>
    const FIELD_BOTTOM_ORDER = ['maintenance_remarks', 'notes'];
    const FIELD_ACRONYMS = ['ms', 'os', 'mac', 'ups', 'avr', 'id'];
    const FIELD_LABEL_OVERRIDES = {
        windows_version: 'OS Version',
        windows_license: 'OS License',
    };

    function formatFieldLabel(key) {
        if (FIELD_LABEL_OVERRIDES[key]) return FIELD_LABEL_OVERRIDES[key];

        return key.split('_').map(word => {
            return FIELD_ACRONYMS.includes(word.toLowerCase())
                ? word.toUpperCase()
                : word.charAt(0).toUpperCase() + word.slice(1).toLowerCase();
        }).join(' ');
    }

    const FIELD_TITLE_CASE_VALUES = ['condition', 'status'];

    function formatFieldValue(key, value) {
        if (typeof value === 'boolean') return value ? 'Yes' : 'No';
        if (value === null || value === '' || value === undefined) return '—';

        if (FIELD_TITLE_CASE_VALUES.includes(key)) {
            return String(value).split(/[_\s]+/)
                .map(w => w.charAt(0).toUpperCase() + w.slice(1).toLowerCase())
                .join(' ');
        }

        return value;
    }

    function fieldRawValue(field) {
        return (typeof field === 'object' && field !== null && 'value' in field)
            ? field.value
            : field;
    }

    function ucfirst(str) {
        return str ? str.charAt(0).toUpperCase() + str.slice(1) : str;
    }

    const ACTION_CHIP_CLASSES = {
        created: 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
        updated: 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
        deleted: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
        issued: 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
        returned: 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300',
    };

    function actionChipClass(action) {
        return ACTION_CHIP_CLASSES[action] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200';
    }

    function orderedEntries(obj) {
        const entries = Object.entries(obj ?? {});
        const rank = (key) => {
            const i = FIELD_BOTTOM_ORDER.indexOf(key);
            return i === -1 ? -1 : i;
        };

        return entries
            .map((entry, index) => ({ entry, index }))
            .sort((a, b) => {
                const ra = rank(a.entry[0]);
                const rb = rank(b.entry[0]);
                if (ra === -1 && rb === -1) return a.index - b.index;
                if (ra === -1) return -1;
                if (rb === -1) return 1;
                return ra - rb;
            })
            .map(({ entry }) => entry);
    }
</script>

<div
    x-data="{
        showDetails: false,
        selectedLog: null,
        currentBulkIndex: 0,

        openLog(log) {
            this.selectedLog = log;
            this.currentBulkIndex = 0;
            this.showDetails = true;
        },

        nextBulk() {
            if (this.selectedLog?.bulk_items && this.currentBulkIndex < this.selectedLog.bulk_items.length - 1) {
                this.currentBulkIndex++;
            }
        },

        previousBulk() {
            if (this.currentBulkIndex > 0) {
                this.currentBulkIndex--;
            }
        },

        get currentBulkItem() {
            return this.selectedLog?.bulk_items?.[this.currentBulkIndex] ?? null;
        }
    }"
    class="space-y-6"
>
    <div>
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Activity Logs</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Audit trail of system actions.
        </p>
    </div>

    {{-- FILTERS --}}
    <form method="GET" class="flex flex-wrap items-end gap-3">
        <div>
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Action</label>
            <select
                id="actionFilter"
                name="action"
                class="mt-1 w-40 rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                onchange="this.form.submit()"
            >
                <option value="">All Actions</option>
                @foreach($actions as $action)
                    <option value="{{ $action }}" @selected(request('action') === $action)>
                        {{ ucfirst($action) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Record type</label>
            <select
                id="recordTypeFilter"
                name="subject_type"
                class="mt-1 w-40 rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                onchange="this.form.submit()"
            >
                @if(!in_array(request('action'), ['issued', 'returned']))
                    <option value="">All Types</option>
                @endif

                @foreach($subjectTypes as $type)
                    <option value="{{ $type }}" @selected(request('subject_type') === $type)>
                        {{ $type }}
                    </option>
                @endforeach
            </select>
        </div>

        @if(request('action') || request('subject_type'))
            <a
                href="{{ route('admin.logs.index') }}"
                class="inline-flex items-center rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600"
            >
                Clear Filters
            </a>
        @endif
    </form>

    {{-- MOBILE VIEW --}}
    <div class="grid gap-3 md:hidden">
        @forelse($logs as $log)
            @php
                $changed = is_array(data_get($log, 'fieldChanges')) ? data_get($log, 'fieldChanges') : [];
                $summary = is_array(data_get($log, 'summary')) ? data_get($log, 'summary') : [];
                $popup = [
                    'action' => $log->action,
                    'description' => $log->description,
                    'subject_type' => $log->subject_type,
                    'is_bulk' => $log->isBulk,
                    'bulk_record_type' => $log->bulkRecordType,
                    'bulk_items' => $log->bulkItems,
                    'summary' => $summary,
                    'changes' => $changed,
                    'meta' => [
                        'user' => $log->user_name ?? 'System',
                        'time' => optional($log->created_at)->format('M d, Y h:i A'),
                    ],
                ];
            @endphp

            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-start gap-3">
                    <div class="flex min-w-0 flex-1 items-start gap-2 overflow-hidden">
                        <span>{{ $actionIcon($log->action) }}</span>
                        <div class="min-w-0 flex-1 overflow-hidden">
                            <div class="w-full break-all text-sm font-medium text-gray-900 dark:text-white">
                                {{ $log->description }}
                            </div>
                            <div class="text-xs text-gray-400 dark:text-gray-500">
                                {{ $log->user_name ?? 'System' }} · {{ $log->created_at->format('M d, Y h:i A') }}
                            </div>
                        </div>
                    </div>

                    <button type="button" class="text-gray-400 hover:text-blue-600 dark:hover:text-blue-400" @click="openLog(@js($popup))">
                        ℹ️
                    </button>
                </div>
            </div>
        @empty
            <div class="rounded-2xl border border-gray-200 bg-white p-6 text-center text-gray-500 shadow-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                No activity recorded yet.
            </div>
        @endforelse
    </div>

    {{-- DESKTOP TABLE --}}
    <div class="hidden overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800 md:block">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 text-left text-sm dark:bg-gray-900/40">
                    <tr>
                        <th class="p-3 font-semibold text-gray-700 dark:text-gray-300">Time</th>
                        <th class="p-3 font-semibold text-gray-700 dark:text-gray-300">User</th>
                        <th class="p-3 font-semibold text-gray-700 dark:text-gray-300">Action</th>
                        <th class="p-3 font-semibold text-gray-700 dark:text-gray-300">Description</th>
                        <th class="p-3 w-20"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-sm dark:divide-gray-700">
                    @forelse($logs as $log)
                        @php
                            $changed = is_array(data_get($log, 'fieldChanges')) ? data_get($log, 'fieldChanges') : [];
                            $summary = is_array(data_get($log, 'summary')) ? data_get($log, 'summary') : [];
                            $popup = [
                                'action' => $log->action,
                                'description' => $log->description,
                                'subject_type' => $log->subject_type,
                                'is_bulk' => $log->isBulk,
                                'bulk_record_type' => $log->bulkRecordType,
                                'bulk_items' => $log->bulkItems,
                                'summary' => $summary,
                                'changes' => $changed,
                                'meta' => [
                                    'user' => $log->user_name ?? 'System',
                                    'time' => optional($log->created_at)->format('M d, Y h:i A'),
                                ],
                            ];
                        @endphp

                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                {{ $log->created_at->format('M d, Y h:i A') }}
                            </td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                                {{ $log->user_name ?? 'System' }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="rounded px-2 py-1 text-xs {{ $chipClass($log->action) }}">
                                    {{ ucfirst($log->action) }}
                                </span>
                            </td>
                            <td class="max-w-0 break-all px-4 py-3 text-gray-900 dark:text-white">
                                {{ $log->description }}
                            </td>
                            <td class="px-4 py-3 text-right text-sm text-gray-700 dark:text-gray-300">
                                <button class="text-gray-400 hover:text-blue-600 dark:hover:text-blue-400" @click="openLog(@js($popup))">
                                    ℹ️
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                No activity recorded yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <style>
        .log-modal-scroll {
            scrollbar-width: thin;
            scrollbar-color: #d1d5db transparent;
        }

        .log-modal-scroll::-webkit-scrollbar {
            width: 8px;
        }

        .log-modal-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        .log-modal-scroll::-webkit-scrollbar-thumb {
            background-color: #d1d5db;
            border-radius: 9999px;
        }

        .log-modal-scroll::-webkit-scrollbar-thumb:hover {
            background-color: #9ca3af;
        }
    </style>

    {{-- MODAL --}}
    <div x-show="showDetails" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true" role="dialog">
        <div class="fixed inset-0 bg-black/40" @click="showDetails = false"></div>

        <div class="flex min-h-full items-start justify-center p-3 sm:items-center sm:p-6">
            <div class="relative mt-6 flex max-h-[85vh] w-full max-w-xl flex-col overflow-hidden rounded-xl bg-white shadow-xl ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700 sm:mt-0">
                <div class="flex shrink-0 items-center justify-between border-b border-gray-200 px-5 py-4 dark:border-gray-700">
                    <h2 class="font-semibold text-gray-900 dark:text-white">Activity Details</h2>
                    <button class="text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white" @click="showDetails = false">✕</button>
                </div>

                <div class="log-modal-scroll min-h-0 flex-1 overflow-y-auto overflow-x-hidden px-5 py-4 text-gray-900 dark:text-gray-100">
                    <template x-if="selectedLog?.is_bulk">
                        <div class="mt-2">
                            <div class="border-b-2 border-gray-200 pb-2 dark:border-gray-700">
                                <h3 class="text-sm font-bold text-gray-800 dark:text-gray-100">Bulk Record</h3>
                            </div>

                            <div class="mt-3 flex items-center justify-between">
                                <button
                                    type="button"
                                    @click="previousBulk()"
                                    :disabled="currentBulkIndex === 0"
                                    class="flex h-9 w-9 items-center justify-center rounded-lg border transition"
                                    :class="currentBulkIndex === 0
                                        ? 'cursor-not-allowed border-gray-200 bg-gray-100 text-gray-400 opacity-40 dark:border-gray-700 dark:bg-gray-700 dark:text-gray-500'
                                        : 'border-gray-300 bg-white hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:hover:bg-gray-600'"
                                >
                                    ◀
                                </button>

                                <div class="text-center">
                                    <div class="text-sm font-semibold text-gray-800 dark:text-gray-100" x-text="selectedLog.bulk_record_type"></div>
                                    <div class="mt-1 text-xs text-gray-500 dark:text-gray-400" x-text="`${currentBulkIndex + 1} of ${selectedLog.bulk_items.length}`"></div>
                                </div>

                                <button
                                    type="button"
                                    @click="nextBulk()"
                                    :disabled="currentBulkIndex >= selectedLog.bulk_items.length - 1"
                                    class="flex h-9 w-9 items-center justify-center rounded-lg border transition"
                                    :class="currentBulkIndex >= selectedLog.bulk_items.length - 1
                                        ? 'cursor-not-allowed border-gray-200 bg-gray-100 text-gray-400 opacity-40 dark:border-gray-700 dark:bg-gray-700 dark:text-gray-500'
                                        : 'border-gray-300 bg-white hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:hover:bg-gray-600'"
                                >
                                    ▶
                                </button>
                            </div>
                        </div>
                    </template>

                    <template x-if="selectedLog">
                        <div class="space-y-8">
                            {{-- BASIC INFO --}}
                            <div class="space-y-3">
                                <div>
                                    <div class="text-xs text-gray-400">Action</div>
                                    <span class="mt-1 inline-block rounded px-2 py-1 text-xs font-medium" :class="actionChipClass(selectedLog.action)" x-text="ucfirst(selectedLog.action)"></span>
                                </div>

                                <div>
                                    <div class="text-xs text-gray-400">Description</div>
                                    <div class="break-words [overflow-wrap:anywhere]" x-text="selectedLog.description"></div>
                                </div>

                                <div>
                                    <div class="text-xs text-gray-400">User</div>
                                    <div x-text="selectedLog.meta?.user ?? 'System'"></div>
                                </div>

                                <div>
                                    <div class="text-xs text-gray-400">Time</div>
                                    <div x-text="selectedLog.meta?.time ?? '-'"></div>
                                </div>
                            </div>

                            {{-- DETAILS + CHANGED FIELDS --}}
                            <div class="space-y-8">
                                {{-- BULK DETAILS --}}
                                <template x-if="selectedLog.is_bulk">
                                    <div>
                                        <h3 class="border-b-2 border-gray-200 pb-2 text-sm font-bold text-gray-800 dark:border-gray-700 dark:text-gray-100">Details</h3>

                                        <template x-for="([key,value]) in orderedEntries(currentBulkItem.summary)" :key="key">
                                            <div class="border-b border-gray-100 py-3 last:border-b-0 dark:border-gray-700">
                                                <div class="text-xs font-medium text-gray-500 dark:text-gray-400" x-text="formatFieldLabel(key)"></div>
                                                <div class="mt-1 break-words text-sm font-medium text-gray-900 [overflow-wrap:anywhere] dark:text-gray-100" x-text="formatFieldValue(key, value)"></div>
                                            </div>
                                        </template>
                                    </div>
                                </template>

                                {{-- NORMAL DETAILS --}}
                                <template x-if="!selectedLog.is_bulk && Object.keys(selectedLog.summary ?? {}).length">
                                    <div>
                                        <h3 class="border-b-2 border-gray-200 pb-2 text-sm font-bold text-gray-800 dark:border-gray-700 dark:text-gray-100">Details</h3>

                                        <template x-for="([key, field]) in orderedEntries(selectedLog.summary)" :key="key">
                                            <div class="border-b border-gray-100 py-3 last:border-b-0 dark:border-gray-700">
                                                <div class="text-xs font-medium text-gray-500 dark:text-gray-400" x-text="formatFieldLabel(key)"></div>
                                                <div class="mt-1 break-words text-sm font-medium text-gray-900 [overflow-wrap:anywhere] dark:text-gray-100">
                                                    <span x-text="formatFieldValue(key, fieldRawValue(field))"></span>
                                                    <span
                                                        x-show="['updated', 'issued', 'returned'].includes(selectedLog.action) && (field.is_new ?? false)"
                                                        class="ml-1 text-xs font-semibold text-blue-600 dark:text-blue-400"
                                                    >
                                                        (New)
                                                    </span>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>

                                {{-- Changed Fields --}}
                                <template x-if="Object.keys(selectedLog.changes ?? {}).length">
                                    <div>
                                        <h3 class="border-b-2 border-gray-200 pb-2 text-sm font-bold text-gray-800 dark:border-gray-700 dark:text-gray-100">Changed Fields</h3>

                                        <template x-for="([key, change]) in orderedEntries(selectedLog.changes)" :key="key">
                                            <div class="border-b border-gray-100 py-4 last:border-b-0 dark:border-gray-700">
                                                <div class="mb-3 text-sm font-semibold text-gray-800 dark:text-gray-100" x-text="formatFieldLabel(key)"></div>

                                                <div class="text-xs font-medium text-gray-500 dark:text-gray-400">Previous Value</div>
                                                <div class="mb-3 mt-1 break-words rounded-lg border-l-4 border-red-300 bg-gray-50 px-3 py-2 text-sm text-gray-700 [overflow-wrap:anywhere] dark:bg-gray-900/40 dark:text-gray-200" x-text="formatFieldValue(key, change.old)"></div>

                                                <div class="text-xs font-medium text-gray-500 dark:text-gray-400">New Value</div>
                                                <div class="mt-1 break-words rounded-lg border-l-4 border-green-400 bg-gray-50 px-3 py-2 text-sm text-gray-700 [overflow-wrap:anywhere] dark:bg-gray-900/40 dark:text-gray-200" x-text="formatFieldValue(key, change.new)"></div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6">
        <x-activity-pagination :paginator="$logs" />
    </div>
</div>
@endsection
