<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Notification extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'request_id',
        'donation_id',
        'charity_id',
        'type',
        'message',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'type' => 'string',
    ];

    /**
     * Get the user that the notification is for.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the request associated with the notification.
     */
    public function request()
    {
        return $this->belongsTo(DonationRequest::class, 'request_id');
    }

    /**
     * Get the donation associated with the notification.
     */
    public function donation()
    {
        return $this->belongsTo(Donation::class, 'donation_id');
    }

    /**
     * Get the charity associated with the notification.
     */
    public function charity()
    {
        return $this->belongsTo(Charity::class);
    }

    /**
     * Check if the notification is a request update.
     */
    public function isRequestUpdate()
    {
        return $this->type === 'request_update';
    }

    /**
     * Check if the notification is a donation update.
     */
    public function isDonationUpdate()
    {
        return $this->type === 'donation_update';
    }

    /**
     * Check if the notification is an inventory alert.
     */
    public function isInventoryAlert()
    {
        return $this->type === 'inventory_alert';
    }

    /**
     * Check if the notification is a thank you note.
     */
    public function isThankYouNote()
    {
        return $this->type === 'thank_you_note';
    }
}
