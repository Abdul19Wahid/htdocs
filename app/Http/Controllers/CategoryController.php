<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\OwnsRecords;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    use OwnsRecords;

    public function index(Request $request)
    {
        $categories = $request->user()->categories()->withCount('expenses')->orderBy('name')->paginate(20);
        return view('categories.index', compact('categories'));
    }

    public function create() { return view('categories.create', ['category' => new Category()]); }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:80', Rule::unique('categories')->where(fn($q) => $q->where('user_id', $request->user()->id))],
            'icon' => ['nullable','string','max:50'], 'color' => ['nullable','regex:/^#[0-9A-Fa-f]{6}$/'], 'description' => ['nullable','string','max:500'],
        ]);
        $data['user_id'] = $request->user()->id;
        Category::create($data);
        ActivityLogController::record($request, 'created', 'category', null, 'Created category: '.$data['name']);
        return to_route('categories.index')->with('success', 'Category created.');
    }

    public function show(Request $request, Category $category) { $this->authorizeOwner($request, $category); return view('categories.show', compact('category')); }

    public function edit(Request $request, Category $category) { $this->authorizeOwner($request, $category); return view('categories.edit', compact('category')); }

    public function update(Request $request, Category $category)
    {
        $this->authorizeOwner($request, $category);
        $data = $request->validate([
            'name' => ['required','string','max:80', Rule::unique('categories')->ignore($category->id)->where(fn($q) => $q->where('user_id', $request->user()->id))],
            'icon' => ['nullable','string','max:50'], 'color' => ['nullable','regex:/^#[0-9A-Fa-f]{6}$/'], 'description' => ['nullable','string','max:500'],
        ]);
        $category->update($data);
        return to_route('categories.index')->with('success', 'Category updated.');
    }

    public function destroy(Request $request, Category $category)
    {
        $this->authorizeOwner($request, $category); $category->delete();
        return back()->with('success', 'Category deleted.');
    }
}
