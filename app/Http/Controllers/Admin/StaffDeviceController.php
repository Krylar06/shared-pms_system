<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\Device;
use App\Models\DeviceAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffDeviceController extends Controller
{
    public function index(Staff $staff)
    {
        $staff->load('office.college');

        $issued = DeviceAssignment::where('staff_id', $staff->id)
            ->whereNull('returned_at')
            ->with(['device.type'])
            ->orderByDesc('issued_at')
            ->get();

        $availableDevices = Device::with('type')
            ->where('status', 'available')
            ->orderBy('property_number')
            ->get();

        return view('admin.staff.devices', compact('staff', 'issued', 'availableDevices'));
    }

    public function issue(Request $request, Staff $staff)
    {
        $request->validate([
            'device_id' => ['required','exists:devices,id'],
        ]);

        $device = Device::findOrFail($request->device_id);
        if ($device->status !== 'available') {
            return back()->with('error', 'Device not available.');
        }

        DeviceAssignment::create([
            'device_id' => $device->id,
            'staff_id' => $staff->id,
            'issued_by' => Auth::id(),
            'issued_at' => now(),
        ]);

        $device->update(['status' => 'issued']);

        return back()->with('success', 'Device issued.');
    }

    public function return(Staff $staff, DeviceAssignment $assignment)
    {
        abort_unless($assignment->staff_id === $staff->id, 404);

        if ($assignment->returned_at) {
            return back();
        }

        $assignment->load('device');
        $assignment->update(['returned_at' => now()]);
        $assignment->device?->update(['status' => 'available']);

        return back()->with('success', 'Device returned.');
    }
}