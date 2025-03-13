<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Charity extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
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
     * Get the users associated with the charity.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the requests made by the charity.
     */
    public function requests()
    {
        return $this->hasMany(Request::class);
    }

    /**
     * Get the inventory items for the charity.
     */
    public function inventoryItems()
    {
        return $this->hasMany(Inventory::class);
    }

    /**
     * Get the notifications for the charity.
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Check if the charity is pending approval.
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the charity is approved.
     */
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    /**
     * Check if the charity is rejected.
     */
    public function isRejected()
    {
        return $this->status === 'rejected';
    }
}
