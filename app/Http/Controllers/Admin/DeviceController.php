<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDeviceRequest;
use App\Http\Requests\UpdateDeviceRequest;
use App\Models\Device;
use App\Models\DeviceType;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->toString();
        $typeId = $request->integer('type'); // device_type_id

        $devices = Device::query()
            ->with(['type', 'currentAssignment.staff'])
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('property_number', 'like', "%{$q}%")
                        ->orWhere('brand', 'like', "%{$q}%")
                        ->orWhere('mac_address', 'like', "%{$q}%");
                });
            })
            ->when($typeId, fn ($query) => $query->where('device_type_id', $typeId))
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $types = DeviceType::orderBy('name')->get();

        return view('admin.devices.index', compact('devices', 'q', 'typeId', 'types'));
    }

    public function create()
    {
        $types = DeviceType::orderBy('name')->get();
        return view('admin.devices.create', compact('types'));
    }

    public function store(StoreDeviceRequest $request)
    {
        $data = $request->validated();

        // Ensure status default if not provided
        $data['status'] = $data['status'] ?? 'available';

        Device::create($data);

        return redirect()->route('admin.devices.index')->with('success', 'Device created.');
    }

    public function edit(Device $device)
    {
        $device->load('type');
        $types = DeviceType::orderBy('name')->get();

        return view('admin.devices.edit', compact('device', 'types'));
    }

    public function update(UpdateDeviceRequest $request, Device $device)
    {
        $data = $request->validated();
        $device->update($data);

        return redirect()->route('admin.devices.index')->with('success', 'Device updated.');
    }

    public function destroy(Device $device)
    {
        $device->delete();
        return redirect()->route('admin.devices.index')->with('success', 'Device deleted.');
    }

    /**
     * Quick update endpoint used by popup edit on "Issued Devices" page.
     */
  public function quickUpdate(Request $request, Device $device)
{
    $data = $request->validate([
        'property_number' => ['required', 'string', 'max:255', 'unique:devices,property_number,' . $device->id],
        'brand' => ['nullable', 'string', 'max:255'],
        'mac_address' => ['nullable', 'string', 'max:255'],
        'unit_price' => ['nullable', 'numeric', 'min:0'],
        'date_acquired' => ['nullable', 'date'],
        'notes' => ['nullable', 'string'],
        'specs' => ['nullable', 'array'],
        'specs.motherboard' => ['nullable', 'string', 'max:255'],
        'specs.memory' => ['nullable', 'string', 'max:255'],
        'specs.hard_disk' => ['nullable', 'string', 'max:255'],
        'specs.dvd_drive' => ['nullable', 'string', 'max:255'],
    ]);

    $device->update($data);

    return back()->with('success', 'Device updated.');
}
public function show(Device $device)
{
    $device->load(['type', 'currentAssignment.staff.office.college']);

    return view('admin.devices.show', compact('device'));
}
}