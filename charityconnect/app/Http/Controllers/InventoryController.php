<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Item;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class InventoryController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->middleware('auth.charity')->except(['show']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get the charity's inventory
        $inventory = Inventory::where('charity_id', Auth::user()->charity_id)
            ->with('item.category')
            ->get();

        // Group by category
        $inventoryByCategory = $inventory->groupBy('item.category.name');

        return view('inventory.index', compact('inventoryByCategory'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $items = Item::all();
        return view('inventory.create', compact('items'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:0',
        ]);

        // Check if the item already exists in the inventory
        $inventory = Inventory::where('charity_id', Auth::user()->charity_id)
            ->where('item_id', $request->item_id)
            ->first();

        if ($inventory) {
            // Update existing inventory
            $inventory->update([
                'quantity' => $request->quantity,
                'last_updated' => now(),
            ]);
        } else {
            // Create new inventory entry
            $inventory = Inventory::create([
                'id' => Str::uuid(),
                'charity_id' => Auth::user()->charity_id,
                'item_id' => $request->item_id,
                'quantity' => $request->quantity,
                'last_updated' => now(),
            ]);
        }

        return redirect()->route('inventory.index')
            ->with('success', 'Inventory updated successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Inventory $inventory)
    {
        // Check if the user belongs to the charity or is an admin
        if (Auth::user()->charity_id !== $inventory->charity_id && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        return view('inventory.show', compact('inventory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Inventory $inventory)
    {
        // Check if the user belongs to the charity
        if (Auth::user()->charity_id !== $inventory->charity_id) {
            abort(403, 'Unauthorized action.');
        }

        return view('inventory.edit', compact('inventory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Inventory $inventory)
    {
        // Check if the user belongs to the charity
        if (Auth::user()->charity_id !== $inventory->charity_id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        // Update inventory
        $inventory->update([
            'quantity' => $request->quantity,
            'last_updated' => now(),
        ]);

        // Check if inventory is low and create notification if needed
        if ($inventory->isLow()) {
            Notification::create([
                'id' => Str::uuid(),
                'user_id' => Auth::id(),
                'charity_id' => $inventory->charity_id,
                'type' => 'inventory_alert',
                'message' => 'Low inventory alert for ' . $inventory->item->name,
            ]);
        }

        return redirect()->route('inventory.index')
            ->with('success', 'Inventory updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inventory $inventory)
    {
        // Check if the user belongs to the charity
        if (Auth::user()->charity_id !== $inventory->charity_id) {
            abort(403, 'Unauthorized action.');
        }

        // Delete inventory
        $inventory->delete();

        return redirect()->route('inventory.index')
            ->with('success', 'Inventory item removed successfully.');
    }

    /**
     * Create a donation request for low inventory items.
     */
    public function createRequest(Inventory $inventory)
    {
        // Check if the user belongs to the charity
        if (Auth::user()->charity_id !== $inventory->charity_id) {
            abort(403, 'Unauthorized action.');
        }

        return redirect()->route('requests.create', ['item_id' => $inventory->item_id])
            ->with('info', 'Creating a donation request for ' . $inventory->item->name);
    }
}
