<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::query()->latest();

        $action = $request->query('action');

        if ($action) {
            $query->where('action', $action);
        }

        // Issued/Returned logs are always Device logs.
        if (in_array($action, ['issued', 'returned'])) {
            $request->merge([
                'subject_type' => 'Device',
            ]);
        }

        $subjectType = $request->query('subject_type');

        if ($subjectType) {
            $query->where(function ($q) use ($subjectType) {

                // Normal logs
                $q->where('subject_type', $subjectType);

                // Bulk logs
                $q->orWhere('changes->record_type', $subjectType);
            });
        }

        $logs = $query->paginate(25)->withQueryString();

        // Only include actions that actually exist in the log — no blank option,
        // the "Clear filters" button handles resetting.
        $actions = ActivityLog::query()
            ->select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        // Same — only non-null subject types that exist.
        // Issued/Returned logs only apply to Devices.
        if (in_array($action, ['issued', 'returned'])) {

            $subjectTypes = collect(['Device']);

        } else {

            // Same — only non-null subject types that exist.
            $subjectTypes = ActivityLog::all()
                ->flatMap(function ($log) {
                    return array_filter([
                        $log->subject_type,
                        data_get($log->changes, 'record_type'),
                    ]);
                })
                ->unique()
                ->sort()
                ->values();

        }

        return view('admin.logs.index', compact('logs', 'actions', 'subjectTypes'));
    }
}