<?php
// FILE: app/Http/Controllers/Admin/MenuItemController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Addon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuItemController extends Controller
{
    public function __construct() { $this->middleware(['auth','admin']); }

    public function index(Request $request)
    {
        $query = MenuItem::with('category');
        if ($request->filled('category')) $query->where('category_id', $request->category);
        if ($request->filled('search')) $query->where('name', 'like', "%{$request->search}%");
        $items      = $query->orderBy('sort_order')->paginate(20)->withQueryString();
        $categories = Category::all();
        return view('admin.menus.index', compact('items','categories'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('admin.menus.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id'      => ['required','exists:categories,id'],
            'name'             => ['required','string','max:200'],
            'description'      => ['nullable','string'],
            'price'            => ['required','numeric','min:0'],
            'image'            => ['nullable','image','max:3072'],
            'is_available'     => ['nullable','boolean'],
            'is_featured'      => ['nullable','boolean'],
            'allergens'        => ['nullable','array'],
            'preparation_time' => ['nullable','integer','min:1'],
            'sort_order'       => ['nullable','integer'],
            'addons'           => ['nullable','array'],
            'addons.*.name'    => ['required_with:addons','string','max:100'],
            'addons.*.price'   => ['required_with:addons','numeric','min:0'],
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('menu', 'public');
        }
        $data['is_available'] = $request->boolean('is_available', true);
        $data['is_featured']  = $request->boolean('is_featured');

        $item = MenuItem::create($data);

        if (!empty($data['addons'])) {
            foreach ($data['addons'] as $addon) {
                $item->addons()->create(['name' => $addon['name'], 'price' => $addon['price']]);
            }
        }

        return redirect()->route('admin.menus.index')->with('success', 'Menu item created.');
    }

    public function edit(MenuItem $menu)
    {
        $categories = Category::where('is_active', true)->get();
        $menu->load('addons');
        return view('admin.menus.edit', compact('menu','categories'));
    }

    public function update(Request $request, MenuItem $menu)
    {
        $data = $request->validate([
            'category_id'      => ['required','exists:categories,id'],
            'name'             => ['required','string','max:200'],
            'description'      => ['nullable','string'],
            'price'            => ['required','numeric','min:0'],
            'image'            => ['nullable','image','max:3072'],
            'is_available'     => ['nullable','boolean'],
            'is_featured'      => ['nullable','boolean'],
            'allergens'        => ['nullable','array'],
            'preparation_time' => ['nullable','integer','min:1'],
            'sort_order'       => ['nullable','integer'],
        ]);

        if ($request->hasFile('image')) {
            if ($menu->image) Storage::disk('public')->delete($menu->image);
            $data['image'] = $request->file('image')->store('menu', 'public');
        }
        $data['is_available'] = $request->boolean('is_available');
        $data['is_featured']  = $request->boolean('is_featured');

        $menu->update($data);

        // Sync add-ons
        if ($request->has('addons')) {
            $menu->addons()->delete();
            foreach ($request->addons as $addon) {
                if (!empty($addon['name'])) {
                    $menu->addons()->create(['name' => $addon['name'], 'price' => $addon['price'] ?? 0]);
                }
            }
        }

        return redirect()->route('admin.menus.index')->with('success', 'Menu item updated.');
    }

    public function destroy(MenuItem $menu)
    {
        if ($menu->image) Storage::disk('public')->delete($menu->image);
        $menu->delete();
        return back()->with('success', 'Item deleted.');
    }

    public function toggleAvailability(MenuItem $menu)
    {
        $menu->update(['is_available' => !$menu->is_available]);
        return response()->json(['available' => $menu->is_available]);
    }
}
