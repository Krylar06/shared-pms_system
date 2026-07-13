@extends('admin.layouts.app')

@section('title', 'Reports')
@section('page_title', 'Reports')

@section('breadcrumbs')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600 dark:hover:text-blue-400">Dashboard</a>
    <span>/</span>
    <span class="font-medium text-gray-800 dark:text-gray-200">Reports</span>
@endsection

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Reports</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Generate inventory, account, and checklist equipment reports.
        </p>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
        {{-- All Assets --}}
        <a
            href="{{ route('admin.reports.assets') }}"
            class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-md dark:border-gray-700 dark:bg-gray-800 dark:hover:border-blue-500"
        >
            <div class="text-sm font-semibold uppercase tracking-wide text-blue-600 dark:text-blue-400">
                All Assets
            </div>
            <h2 class="mt-3 text-lg font-semibold text-gray-900 dark:text-white">
                Assets by Type / Office / Location
            </h2>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                Filter all equipment by device type, location, office, or keyword.
            </p>
        </a>

        {{-- Accounts: Admin and Unit Head have the same access --}}
        @if(auth()->user() && (auth()->user()->isAdmin() || auth()->user()->isUnitHead()))
            <a
                href="{{ route('admin.reports.accounts') }}"
                class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-violet-200 hover:shadow-md dark:border-gray-700 dark:bg-gray-800 dark:hover:border-violet-500"
            >
                <div class="text-sm font-semibold uppercase tracking-wide text-violet-600 dark:text-violet-400">
                    Accounts
                </div>
                <h2 class="mt-3 text-lg font-semibold text-gray-900 dark:text-white">
                    Registered Admin / Custodian Accounts
                </h2>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    List user accounts by role with searchable names and emails.
                </p>
            </a>
        @endif

        {{-- Checklist Equipment --}}
        <a
            href="{{ route('admin.reports.checkedEquipment') }}"
            class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-emerald-200 hover:shadow-md dark:border-gray-700 dark:bg-gray-800 dark:hover:border-emerald-500"
        >
            <div class="text-sm font-semibold uppercase tracking-wide text-emerald-600 dark:text-emerald-400">
                Checklist Equipment
            </div>
            <h2 class="mt-3 text-lg font-semibold text-gray-900 dark:text-white">
                Checklist Equipment by Admin
            </h2>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                See equipment checklist records, grouped by admin account and date.
            </p>
        </a>
    </div>
</div>
@endsection
