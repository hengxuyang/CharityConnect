<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\DonationRequest;
use App\Models\Item;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DonationRequestController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified'])->except(['index', 'show']);
        $this->middleware('auth.charity')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = DonationRequest::with(['charity', 'item.category'])
            ->where('status', 'Pending');

        // Filter by category if provided
        if ($request->has('category')) {
            $categoryId = $request->category;
            $query->whereHas('item', function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }

        // Filter by request type if provided
        if ($request->has('type')) {
            $query->where('request_type', $request->type);
        }

        $requests = $query->latest()->paginate(10);
        $categories = Category::all();

        return view('requests.index', compact('requests', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        $items = Item::all();
        return view('requests.create', compact('categories', 'items'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'request_type' => 'required|in:drop-off,pickup',
            'drop_off_address' => 'required|string',
            'available_times' => 'required|date',
        ]);

        $charity = Auth::user()->charity;

        $donationRequest = DonationRequest::create([
            'id' => Str::uuid(),
            'charity_id' => $charity->id,
            'item_id' => $request->item_id,
            'quantity' => $request->quantity,
            'request_type' => $request->request_type,
            'drop_off_address' => $request->drop_off_address,
            'available_times' => $request->available_times,
            'status' => 'Pending',
        ]);

        // Create notification for charity
        Notification::create([
            'id' => Str::uuid(),
            'user_id' => Auth::id(),
            'request_id' => $donationRequest->id,
            'charity_id' => $charity->id,
            'type' => 'request_update',
            'message' => 'New donation request created for ' . $donationRequest->item->name,
        ]);

        return redirect()->route('requests.show', $donationRequest)
            ->with('success', 'Donation request created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(DonationRequest $request)
    {
        return view('requests.show', compact('request'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DonationRequest $request)
    {
        // Check if the authenticated user belongs to the charity that created the request
        if (Auth::user()->charity_id !== $request->charity_id && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $categories = Category::all();
        $items = Item::all();
        return view('requests.edit', compact('request', 'categories', 'items'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DonationRequest $donationRequest)
    {
        // Check if the authenticated user belongs to the charity that created the request
        if (Auth::user()->charity_id !== $donationRequest->charity_id && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'request_type' => 'required|in:drop-off,pickup',
            'drop_off_address' => 'required|string',
            'available_times' => 'required|date',
            'status' => 'required|in:Pending,Fulfilled,Cancelled',
        ]);

        $donationRequest->update([
            'item_id' => $request->item_id,
            'quantity' => $request->quantity,
            'request_type' => $request->request_type,
            'drop_off_address' => $request->drop_off_address,
            'available_times' => $request->available_times,
            'status' => $request->status,
        ]);

        // Create notification for status change
        if ($donationRequest->wasChanged('status')) {
            Notification::create([
                'id' => Str::uuid(),
                'user_id' => Auth::id(),
                'request_id' => $donationRequest->id,
                'charity_id' => $donationRequest->charity_id,
                'type' => 'request_update',
                'message' => 'Donation request status updated to ' . $donationRequest->status,
            ]);
        }

        return redirect()->route('requests.show', $donationRequest)
            ->with('success', 'Donation request updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DonationRequest $request)
    {
        // Check if the authenticated user belongs to the charity that created the request
        if (Auth::user()->charity_id !== $request->charity_id && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        // Update status to cancelled instead of deleting
        $request->update(['status' => 'Cancelled']);

        // Create notification
        Notification::create([
            'id' => Str::uuid(),
            'user_id' => Auth::id(),
            'request_id' => $request->id,
            'charity_id' => $request->charity_id,
            'type' => 'request_update',
            'message' => 'Donation request cancelled',
        ]);

        return redirect()->route('requests.index')
            ->with('success', 'Donation request cancelled successfully.');
    }
}
