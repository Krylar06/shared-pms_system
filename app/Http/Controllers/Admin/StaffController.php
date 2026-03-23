<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Office;
use App\Models\Staff;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index(Office $office)
    {
        $staff = Staff::where('office_id', $office->id)
            ->orderBy('last_name')->orderBy('first_name')
            ->paginate(15);

        $office->load('college');

        return view('admin.staff.index', compact('office', 'staff'));
    }

    public function store(Request $request, Office $office)
    {
        $data = $request->validate([
            'first_name' => ['required','string','max:255'],
            'last_name' => ['required','string','max:255'],
            'position' => ['nullable','string','max:255'],
            'email' => ['nullable','email','max:255'],
            'phone' => ['nullable','string','max:255'],
            'is_active' => ['sometimes','boolean'],
        ]);

        Staff::create([
            'office_id' => $office->id,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'position' => $data['position'] ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'is_active' => (bool)($data['is_active'] ?? true),
        ]);

        return back()->with('success', 'Staff created.');
    }

    public function edit(Office $office, Staff $staff)
    {
        abort_unless($staff->office_id === $office->id, 404);
        $office->load('college');

        return view('admin.staff.edit', compact('office', 'staff'));
    }

    public function update(Request $request, Office $office, Staff $staff)
    {
        abort_unless($staff->office_id === $office->id, 404);

        $data = $request->validate([
            'first_name' => ['required','string','max:255'],
            'last_name' => ['required','string','max:255'],
            'position' => ['nullable','string','max:255'],
            'email' => ['nullable','email','max:255'],
            'phone' => ['nullable','string','max:255'],
            'is_active' => ['sometimes','boolean'],
        ]);

        $staff->update([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'position' => $data['position'] ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'is_active' => (bool)($data['is_active'] ?? false),
        ]);

        $office->load('college');

        return redirect()->route('admin.staff.index', $office)->with('success', 'Staff updated.');
    }

    public function destroy(Office $office, Staff $staff)
    {
        abort_unless($staff->office_id === $office->id, 404);
        $staff->delete();
        return back()->with('success', 'Staff deleted.');
    }
}