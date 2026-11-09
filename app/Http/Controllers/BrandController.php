<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BrandController extends Controller
{
    public function index(): View
    {
        $brands = Brand::orderBy('name')->paginate(20);

        return view('brands.index', compact('brands'));
    }

    public function create(): View
    {
        return view('brands.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Brand::create($request->validate(['name' => ['required', 'string', 'max:100']]));

        return redirect()->route('brands.index')->with('status', 'Brand saved.');
    }

    public function edit(Brand $brand): View
    {
        return view('brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand): RedirectResponse
    {
        $brand->update($request->validate(['name' => ['required', 'string', 'max:100']]));

        return redirect()->route('brands.index')->with('status', 'Brand updated.');
    }

    public function destroy(Brand $brand): RedirectResponse
    {
        $brand->delete();

        return back()->with('status', 'Brand deleted.');
    }
}
