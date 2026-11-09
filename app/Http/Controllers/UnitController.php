<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UnitController extends Controller
{
    public function index(): View
    {
        $units = Unit::orderBy('name')->paginate(20);

        return view('units.index', compact('units'));
    }

    public function create(): View
    {
        return view('units.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Unit::create($request->validate(['name' => ['required', 'string', 'max:50'], 'short_name' => ['required', 'string', 'max:10']]));

        return redirect()->route('units.index')->with('status', 'Unit saved.');
    }

    public function edit(Unit $unit): View
    {
        return view('units.edit', compact('unit'));
    }

    public function update(Request $request, Unit $unit): RedirectResponse
    {
        $unit->update($request->validate(['name' => ['required', 'string', 'max:50'], 'short_name' => ['required', 'string', 'max:10']]));

        return redirect()->route('units.index')->with('status', 'Unit updated.');
    }

    public function destroy(Unit $unit): RedirectResponse
    {
        $unit->delete();

        return back()->with('status', 'Unit deleted.');
    }
}
