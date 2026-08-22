<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Collateral extends Model
{
    protected $table = 'collaterals';
    protected $primaryKey = 'collateral_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'collateral_id',
        'type_id',
        'name',
        'location_status',
        'current_slot_id',
        'status',
    ];

    public function type(): BelongsTo
    {
        return $this->belongsTo(CollateralType::class, 'type_id', 'type_id');
    }

    public function currentSlot(): BelongsTo
    {
        return $this->belongsTo(CabinetSlot::class, 'current_slot_id', 'slot_id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'product_collaterals',
            'collateral_id',
            'product_id'
        );
    }

    public function ticketItems(): HasMany
    {
        return $this->hasMany(TicketItem::class, 'collateral_id', 'collateral_id');
    }
}
