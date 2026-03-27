<?php
// FILE: app/Http/Controllers/Admin/CategoryController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function __construct() { $this->middleware(['auth','admin']); }

    public function index()
    {
        $categories = Category::withCount('menuItems')->orderBy('sort_order')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function create() { return view('admin.categories.create'); }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required','string','max:100'],
            'description' => ['nullable','string'],
            'image'       => ['nullable','image','max:2048'],
            'sort_order'  => ['nullable','integer'],
            'is_active'   => ['boolean'],
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        Category::create($data);
        return redirect()->route('admin.categories.index')->with('success', 'Category created.');
    }

    public function edit(Category $category) { return view('admin.categories.edit', compact('category')); }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name'        => ['required','string','max:100'],
            'description' => ['nullable','string'],
            'image'       => ['nullable','image','max:2048'],
            'sort_order'  => ['nullable','integer'],
            'is_active'   => ['nullable','boolean'],
        ]);

        if ($request->hasFile('image')) {
            if ($category->image) Storage::disk('public')->delete($category->image);
            $data['image'] = $request->file('image')->store('categories', 'public');
        }
        $data['is_active'] = $request->boolean('is_active');

        $category->update($data);
        return redirect()->route('admin.categories.index')->with('success', 'Category updated.');
    }

    public function destroy(Category $category)
    {
        if ($category->image) Storage::disk('public')->delete($category->image);
        $category->delete();
        return back()->with('success', 'Category deleted.');
    }
}
