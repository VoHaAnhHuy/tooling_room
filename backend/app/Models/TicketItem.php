<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketItem extends Model
{
    protected $table = 'ticket_items';
    protected $primaryKey = 'item_id';
    public $timestamps = false;

    protected $fillable = [
        'ticket_id',
        'collateral_id',
        'from_slot_id',
        'to_slot_id',
        'condition_description',
        'image_url',
        'verified_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'ticket_id', 'ticket_id');
    }

    public function collateral(): BelongsTo
    {
        return $this->belongsTo(Collateral::class, 'collateral_id', 'collateral_id');
    }

    public function fromSlot(): BelongsTo
    {
        return $this->belongsTo(CabinetSlot::class, 'from_slot_id', 'slot_id');
    }

    public function toSlot(): BelongsTo
    {
        return $this->belongsTo(CabinetSlot::class, 'to_slot_id', 'slot_id');
    }
}
