<?php
// FILE: app/Http/Controllers/MenuController.php
namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\MenuItem;

class MenuController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'approved']);
    }

    public function index()
    {
        $categories = Category::with(['activeMenuItems.availableAddons'])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('user.menu.index', compact('categories'));
    }

    public function item(MenuItem $item)
    {
        if (!$item->is_available) {
            abort(404);
        }
        $item->load('availableAddons', 'category');
        return view('user.menu.item', compact('item'));
    }
}
