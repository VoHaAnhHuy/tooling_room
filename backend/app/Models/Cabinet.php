<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cabinet extends Model
{
    protected $table = 'cabinets';
    protected $primaryKey = 'cabinet_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'cabinet_id',
        'cabinet_name',
        'total_rows',
        'total_columns',
    ];

    public function slots(): HasMany
    {
        return $this->hasMany(CabinetSlot::class, 'cabinet_id', 'cabinet_id');
    }
}
