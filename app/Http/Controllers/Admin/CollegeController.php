<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\College;
use Illuminate\Http\Request;

class CollegeController extends Controller
{
    public function index()
    {
        $colleges = College::orderBy('name')->paginate(15);
        return view('admin.colleges.index', compact('colleges'));
    }

    public function create()
    {
        return view('admin.colleges.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'code' => ['nullable','string','max:255'],
        ]);

        College::create($data);

        return redirect()->route('admin.colleges.index')->with('success', 'College created.');
    }

    public function edit(College $college)
    {
        return view('admin.colleges.edit', compact('college'));
    }

    public function update(Request $request, College $college)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'code' => ['nullable','string','max:255'],
        ]);

        $college->update($data);

        return redirect()->route('admin.colleges.index')->with('success', 'College updated.');
    }

    public function destroy(College $college)
    {
        $college->delete();
        return back()->with('success', 'College deleted.');
    }
}