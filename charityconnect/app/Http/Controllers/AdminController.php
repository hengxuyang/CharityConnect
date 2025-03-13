<?php

namespace App\Http\Controllers;

use App\Models\Charity;
use App\Models\Donation;
use App\Models\DonationRequest;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isAdmin()) {
                abort(403, 'Unauthorized action.');
            }
            return $next($request);
        });
    }

    /**
     * Display the admin dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        $stats = [
            'charities' => [
                'total' => Charity::count(),
                'pending' => Charity::where('status', 'pending')->count(),
                'approved' => Charity::where('status', 'approved')->count(),
                'rejected' => Charity::where('status', 'rejected')->count(),
            ],
            'users' => [
                'total' => User::count(),
                'admins' => User::where('role', 'admin')->count(),
                'charities' => User::where('role', 'charity')->count(),
                'public_users' => User::where('role', 'public_user')->count(),
            ],
            'requests' => [
                'total' => DonationRequest::count(),
                'pending' => DonationRequest::where('status', 'Pending')->count(),
                'fulfilled' => DonationRequest::where('status', 'Fulfilled')->count(),
                'cancelled' => DonationRequest::where('status', 'Cancelled')->count(),
            ],
            'donations' => [
                'total' => Donation::count(),
                'pending' => Donation::where('status', 'Pending')->count(),
                'delivered' => Donation::where('status', 'Delivered')->count(),
                'cancelled' => Donation::where('status', 'Cancelled')->count(),
            ],
        ];

        $pendingCharities = Charity::where('status', 'pending')->get();

        return view('admin.dashboard', compact('stats', 'pendingCharities'));
    }

    /**
     * Display a list of charities for approval.
     *
     * @return \Illuminate\Http\Response
     */
    public function charities()
    {
        $charities = Charity::all();
        return view('admin.charities', compact('charities'));
    }

    /**
     * Approve a charity.
     *
     * @param  \App\Models\Charity  $charity
     * @return \Illuminate\Http\Response
     */
    public function approveCharity(Charity $charity)
    {
        $charity->status = 'approved';
        $charity->save();

        return redirect()->route('admin.charities')->with('success', 'Charity approved successfully.');
    }

    /**
     * Reject a charity.
     *
     * @param  \App\Models\Charity  $charity
     * @return \Illuminate\Http\Response
     */
    public function rejectCharity(Charity $charity)
    {
        $charity->status = 'rejected';
        $charity->save();

        return redirect()->route('admin.charities')->with('success', 'Charity rejected successfully.');
    }

    /**
     * Display a list of users.
     *
     * @return \Illuminate\Http\Response
     */
    public function users()
    {
        $users = User::all();
        return view('admin.users', compact('users'));
    }
}
