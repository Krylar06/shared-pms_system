<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\College;
use App\Models\Office;
use Illuminate\Http\Request;

class OfficeController extends Controller
{
    public function index(College $college)
    {
        $offices = Office::where('college_id', $college->id)->orderBy('name')->paginate(15);
        return view('admin.offices.index', compact('college', 'offices'));
    }

    public function store(Request $request, College $college)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
        ]);

        Office::create([
            'college_id' => $college->id,
            'name' => $data['name'],
        ]);

        return back()->with('success', 'Office created.');
    }

    public function edit(College $college, Office $office)
    {
        abort_unless($office->college_id === $college->id, 404);
        return view('admin.offices.edit', compact('college', 'office'));
    }

    public function update(Request $request, College $college, Office $office)
    {
        abort_unless($office->college_id === $college->id, 404);

        $data = $request->validate([
            'name' => ['required','string','max:255'],
        ]);

        $office->update($data);

        return redirect()->route('admin.offices.index', $college)->with('success', 'Office updated.');
    }

    public function destroy(College $college, Office $office)
    {
        abort_unless($office->college_id === $college->id, 404);
        $office->delete();
        return back()->with('success', 'Office deleted.');
    }
}