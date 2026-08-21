<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CabinetSlot extends Model
{
    protected $table = 'cabinet_slots';
    protected $primaryKey = 'slot_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'slot_id',
        'cabinet_id',
        'row_index',
        'column_index',
    ];

    public function cabinet(): BelongsTo
    {
        return $this->belongsTo(Cabinet::class, 'cabinet_id', 'cabinet_id');
    }

    public function currentCollateral(): HasOne
    {
        return $this->hasOne(Collateral::class, 'current_slot_id', 'slot_id');
    }

    public function ticketItemsFrom(): HasMany
    {
        return $this->hasMany(TicketItem::class, 'from_slot_id', 'slot_id');
    }

    public function ticketItemsTo(): HasMany
    {
        return $this->hasMany(TicketItem::class, 'to_slot_id', 'slot_id');
    }
}
