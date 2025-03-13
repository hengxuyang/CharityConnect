<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Item extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'category_id',
        'unit',
        'expiry_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expiry_date' => 'date',
    ];

    /**
     * Get the category that the item belongs to.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the requests for this item.
     */
    public function requests()
    {
        return $this->hasMany(Request::class);
    }

    /**
     * Get the inventory entries for this item.
     */
    public function inventoryEntries()
    {
        return $this->hasMany(Inventory::class);
    }
}
