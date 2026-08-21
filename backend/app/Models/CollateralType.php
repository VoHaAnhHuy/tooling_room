<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CollateralType extends Model
{
    protected $table = 'collateral_types';
    protected $primaryKey = 'type_id';
    public $timestamps = false;

    protected $fillable = [
        'type_name',
    ];

    public function collaterals(): HasMany
    {
        return $this->hasMany(Collateral::class, 'type_id', 'type_id');
    }
}
