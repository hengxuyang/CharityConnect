<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class DonationRequest extends Model
{
    use HasFactory, HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'requests';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'charity_id',
        'item_id',
        'quantity',
        'request_type',
        'drop_off_address',
        'available_times',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'available_times' => 'datetime',
        'request_type' => 'string',
        'status' => 'string',
    ];

    /**
     * Get the charity that made the request.
     */
    public function charity()
    {
        return $this->belongsTo(Charity::class);
    }

    /**
     * Get the item that is being requested.
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Get the donations for this request.
     */
    public function donations()
    {
        return $this->hasMany(Donation::class, 'request_id');
    }

    /**
     * Get the notifications for this request.
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'request_id');
    }

    /**
     * Check if the request is pending.
     */
    public function isPending()
    {
        return $this->status === 'Pending';
    }

    /**
     * Check if the request is fulfilled.
     */
    public function isFulfilled()
    {
        return $this->status === 'Fulfilled';
    }

    /**
     * Check if the request is cancelled.
     */
    public function isCancelled()
    {
        return $this->status === 'Cancelled';
    }
}
