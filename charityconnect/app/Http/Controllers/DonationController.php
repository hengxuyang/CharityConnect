<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\DonationRequest;
use App\Models\Inventory;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DonationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Show donations made by the current user
        $donations = Donation::where('donor_id', Auth::id())
            ->with(['request.charity', 'request.item'])
            ->latest()
            ->paginate(10);

        return view('donations.index', compact('donations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // Get the donation request
        $donationRequest = DonationRequest::findOrFail($request->request_id);

        // Check if the request is still pending
        if ($donationRequest->status !== 'Pending') {
            return redirect()->route('requests.show', $donationRequest)
                ->with('error', 'This donation request is no longer accepting donations.');
        }

        return view('donations.create', compact('donationRequest'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'request_id' => 'required|exists:requests,id',
            'quantity' => 'required|integer|min:1',
        ]);

        // Get the donation request
        $donationRequest = DonationRequest::findOrFail($request->request_id);

        // Check if the request is still pending
        if ($donationRequest->status !== 'Pending') {
            return redirect()->route('requests.show', $donationRequest)
                ->with('error', 'This donation request is no longer accepting donations.');
        }

        // Check if the quantity is valid
        if ($request->quantity > $donationRequest->quantity) {
            return redirect()->route('donations.create', ['request_id' => $donationRequest->id])
                ->with('error', 'The donation quantity cannot exceed the requested quantity.');
        }

        // Create the donation
        $donation = Donation::create([
            'id' => Str::uuid(),
            'donor_id' => Auth::id(),
            'request_id' => $donationRequest->id,
            'quantity' => $request->quantity,
            'status' => 'Pending',
        ]);

        // Create notification for the charity
        Notification::create([
            'id' => Str::uuid(),
            'user_id' => $donationRequest->charity->users->first()->id,
            'request_id' => $donationRequest->id,
            'donation_id' => $donation->id,
            'charity_id' => $donationRequest->charity_id,
            'type' => 'donation_update',
            'message' => 'New donation received for ' . $donationRequest->item->name,
        ]);

        return redirect()->route('donations.show', $donation)
            ->with('success', 'Donation submitted successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Donation $donation)
    {
        // Check if the user is the donor or belongs to the charity
        if (Auth::id() !== $donation->donor_id && 
            Auth::user()->charity_id !== $donation->request->charity_id && 
            !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        return view('donations.show', compact('donation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Donation $donation)
    {
        // Check if the user is the donor
        if (Auth::id() !== $donation->donor_id && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        // Check if the donation is still pending
        if ($donation->status !== 'Pending') {
            return redirect()->route('donations.show', $donation)
                ->with('error', 'This donation can no longer be edited.');
        }

        return view('donations.edit', compact('donation'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Donation $donation)
    {
        // Check if the user is the donor or belongs to the charity
        $isCharity = Auth::user()->charity_id === $donation->request->charity_id;
        $isDonor = Auth::id() === $donation->donor_id;
        $isAdmin = Auth::user()->isAdmin();

        if (!$isDonor && !$isCharity && !$isAdmin) {
            abort(403, 'Unauthorized action.');
        }

        // Different validation rules based on user role
        if ($isDonor || $isAdmin) {
            $request->validate([
                'quantity' => 'required|integer|min:1',
                'status' => 'required|in:Pending,Cancelled',
            ]);
        } else {
            $request->validate([
                'status' => 'required|in:Pending,Delivered,Cancelled',
            ]);
        }

        // Update the donation
        $donation->update([
            'quantity' => $request->has('quantity') ? $request->quantity : $donation->quantity,
            'status' => $request->status,
        ]);

        // If the donation is marked as delivered, update the inventory
        if ($request->status === 'Delivered' && $donation->status !== $request->status) {
            $this->updateInventory($donation);
        }

        // Create notification
        $notificationUser = $isCharity ? $donation->donor_id : $donation->request->charity->users->first()->id;
        Notification::create([
            'id' => Str::uuid(),
            'user_id' => $notificationUser,
            'request_id' => $donation->request_id,
            'donation_id' => $donation->id,
            'charity_id' => $donation->request->charity_id,
            'type' => 'donation_update',
            'message' => 'Donation status updated to ' . $donation->status,
        ]);

        return redirect()->route('donations.show', $donation)
            ->with('success', 'Donation updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Donation $donation)
    {
        // Check if the user is the donor
        if (Auth::id() !== $donation->donor_id && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        // Check if the donation is still pending
        if ($donation->status !== 'Pending') {
            return redirect()->route('donations.show', $donation)
                ->with('error', 'This donation can no longer be cancelled.');
        }

        // Update status to cancelled instead of deleting
        $donation->update(['status' => 'Cancelled']);

        // Create notification
        Notification::create([
            'id' => Str::uuid(),
            'user_id' => $donation->request->charity->users->first()->id,
            'request_id' => $donation->request_id,
            'donation_id' => $donation->id,
            'charity_id' => $donation->request->charity_id,
            'type' => 'donation_update',
            'message' => 'Donation cancelled',
        ]);

        return redirect()->route('donations.index')
            ->with('success', 'Donation cancelled successfully.');
    }

    /**
     * Update the inventory when a donation is delivered.
     */
    private function updateInventory(Donation $donation)
    {
        $charity = $donation->request->charity;
        $item = $donation->request->item;

        // Check if the item already exists in the inventory
        $inventory = Inventory::where('charity_id', $charity->id)
            ->where('item_id', $item->id)
            ->first();

        if ($inventory) {
            // Update existing inventory
            $inventory->update([
                'quantity' => $inventory->quantity + $donation->quantity,
                'last_updated' => now(),
            ]);
        } else {
            // Create new inventory entry
            Inventory::create([
                'id' => Str::uuid(),
                'charity_id' => $charity->id,
                'item_id' => $item->id,
                'quantity' => $donation->quantity,
                'last_updated' => now(),
            ]);
        }

        // Check if the request is fulfilled
        $totalDonated = Donation::where('request_id', $donation->request_id)
            ->where('status', 'Delivered')
            ->sum('quantity');

        if ($totalDonated >= $donation->request->quantity) {
            $donation->request->update(['status' => 'Fulfilled']);

            // Create notification for fulfilled request
            Notification::create([
                'id' => Str::uuid(),
                'user_id' => $charity->users->first()->id,
                'request_id' => $donation->request_id,
                'charity_id' => $charity->id,
                'type' => 'request_update',
                'message' => 'Donation request for ' . $item->name . ' has been fulfilled',
            ]);
        }
    }

    /**
     * Send a thank you note to a donor.
     */
    public function sendThankYou(Request $request, Donation $donation)
    {
        // Check if the user belongs to the charity
        if (Auth::user()->charity_id !== $donation->request->charity_id && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'message' => 'required|string|max:500',
        ]);

        // Create thank you notification
        Notification::create([
            'id' => Str::uuid(),
            'user_id' => $donation->donor_id,
            'donation_id' => $donation->id,
            'charity_id' => $donation->request->charity_id,
            'type' => 'thank_you_note',
            'message' => $request->message,
        ]);

        return redirect()->route('donations.show', $donation)
            ->with('success', 'Thank you note sent successfully.');
    }
}
