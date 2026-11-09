<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::orderBy('name')->paginate(20);

        return view('categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Category::create($request->validate(['name' => ['required', 'string', 'max:100'], 'description' => ['nullable', 'string']]));

        return redirect()->route('categories.index')->with('status', 'Category saved.');
    }

    public function edit(Category $categorie): View
    {
        return view('categories.edit', compact('categorie'));
    }

    public function update(Request $request, Category $categorie): RedirectResponse
    {
        $categorie->update($request->validate(['name' => ['required', 'string', 'max:100'], 'description' => ['nullable', 'string']]));

        return redirect()->route('categories.index')->with('status', 'Category updated.');
    }

    public function destroy(Category $categorie): RedirectResponse
    {
        $categorie->delete();

        return back()->with('status', 'Category deleted.');
    }
}
