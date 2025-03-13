<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Donation extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'donor_id',
        'request_id',
        'quantity',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Get the donor that made the donation.
     */
    public function donor()
    {
        return $this->belongsTo(User::class, 'donor_id');
    }

    /**
     * Get the request that this donation is for.
     */
    public function request()
    {
        return $this->belongsTo(DonationRequest::class, 'request_id');
    }

    /**
     * Get the notifications for this donation.
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'donation_id');
    }

    /**
     * Check if the donation is pending.
     */
    public function isPending()
    {
        return $this->status === 'Pending';
    }

    /**
     * Check if the donation is delivered.
     */
    public function isDelivered()
    {
        return $this->status === 'Delivered';
    }

    /**
     * Check if the donation is cancelled.
     */
    public function isCancelled()
    {
        return $this->status === 'Cancelled';
    }
}
