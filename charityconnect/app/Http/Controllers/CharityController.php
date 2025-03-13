<?php

namespace App\Http\Controllers;

use App\Models\Charity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CharityController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified'])->except(['index', 'show', 'create', 'store']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $charities = Charity::where('status', 'approved')->get();
        return view('charities.index', compact('charities'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('charities.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:charities',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'user_name' => 'required|string|max:255',
            'user_email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Create charity
        $charity = Charity::create([
            'id' => Str::uuid(),
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'status' => 'pending',
        ]);

        // Create user associated with charity
        $user = User::create([
            'id' => Str::uuid(),
            'name' => $request->user_name,
            'email' => $request->user_email,
            'password' => Hash::make($request->password),
            'role' => 'charity',
            'charity_id' => $charity->id,
        ]);

        // Log in the user
        Auth::login($user);

        return redirect()->route('charities.show', $charity)->with('success', 'Charity registration submitted successfully. Your application is pending approval.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Charity $charity)
    {
        // Only show approved charities to the public
        if ($charity->status !== 'approved' && (!Auth::check() || (Auth::user()->charity_id !== $charity->id && !Auth::user()->isAdmin()))) {
            abort(404);
        }

        return view('charities.show', compact('charity'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Charity $charity)
    {
        // Only charity owners and admins can edit
        if (Auth::user()->charity_id !== $charity->id && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        return view('charities.edit', compact('charity'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Charity $charity)
    {
        // Only charity owners and admins can update
        if (Auth::user()->charity_id !== $charity->id && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:charities,email,' . $charity->id,
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
        ]);

        $charity->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        return redirect()->route('charities.show', $charity)->with('success', 'Charity updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Charity $charity)
    {
        // Only admins can delete charities
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        // Delete associated users
        User::where('charity_id', $charity->id)->delete();

        // Delete charity
        $charity->delete();

        return redirect()->route('charities.index')->with('success', 'Charity deleted successfully.');
    }

    /**
     * Display the charity dashboard.
     */
    public function dashboard()
    {
        // Only charity users can access their dashboard
        if (!Auth::user()->isCharity()) {
            abort(403, 'Unauthorized action.');
        }

        $charity = Auth::user()->charity;
        
        // Get charity's donation requests
        $requests = $charity->requests()->latest()->get();
        
        // Get charity's inventory
        $inventory = $charity->inventoryItems()->with('item')->get();

        return view('charities.dashboard', compact('charity', 'requests', 'inventory'));
    }
}
