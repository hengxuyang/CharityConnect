<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Inventory extends Model
{
    use HasFactory, HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'inventory';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'charity_id',
        'item_id',
        'quantity',
        'last_updated',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'last_updated' => 'datetime',
    ];

    /**
     * Get the charity that owns the inventory.
     */
    public function charity()
    {
        return $this->belongsTo(Charity::class);
    }

    /**
     * Get the item in the inventory.
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Check if the inventory is low.
     */
    public function isLow()
    {
        // This is a placeholder. The actual threshold would depend on the item type.
        return $this->quantity <= 10;
    }

    /**
     * Check if the inventory is urgent.
     */
    public function isUrgent()
    {
        // This is a placeholder. The actual threshold would depend on the item type.
        return $this->quantity <= 5;
    }
}
