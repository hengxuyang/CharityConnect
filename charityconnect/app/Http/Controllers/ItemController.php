<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ItemController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->middleware('can:admin')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Item::with('category');

        // Filter by category if provided
        if ($request->has('category')) {
            $query->where('category_id', $request->category);
        }

        // Search by name if provided
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $items = $query->paginate(10);
        $categories = Category::all();

        return view('items.index', compact('items', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Only admins can create items
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $categories = Category::all();
        return view('items.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Only admins can create items
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'unit' => 'required|string|max:50',
            'expiry_date' => 'nullable|date',
        ]);

        $item = Item::create([
            'id' => Str::uuid(),
            'name' => $request->name,
            'category_id' => $request->category_id,
            'unit' => $request->unit,
            'expiry_date' => $request->expiry_date,
        ]);

        return redirect()->route('items.index')
            ->with('success', 'Item created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item)
    {
        // Get donation requests for this item
        $requests = $item->requests()
            ->where('status', 'Pending')
            ->with('charity')
            ->paginate(5);

        return view('items.show', compact('item', 'requests'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Item $item)
    {
        // Only admins can edit items
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $categories = Category::all();
        return view('items.edit', compact('item', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Item $item)
    {
        // Only admins can update items
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'unit' => 'required|string|max:50',
            'expiry_date' => 'nullable|date',
        ]);

        $item->update([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'unit' => $request->unit,
            'expiry_date' => $request->expiry_date,
        ]);

        return redirect()->route('items.index')
            ->with('success', 'Item updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item)
    {
        // Only admins can delete items
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        // Check if the item is used in any requests or inventory
        if ($item->requests()->count() > 0 || $item->inventoryEntries()->count() > 0) {
            return redirect()->route('items.index')
                ->with('error', 'Cannot delete item that is used in requests or inventory.');
        }

        $item->delete();

        return redirect()->route('items.index')
            ->with('success', 'Item deleted successfully.');
    }
}
