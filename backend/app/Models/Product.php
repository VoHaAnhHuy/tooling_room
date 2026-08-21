<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'product_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'product_name',
    ];

    public function collaterals(): BelongsToMany
    {
        return $this->belongsToMany(
            Collateral::class,
            'product_collaterals',
            'product_id',
            'collateral_id'
        )->withoutPivot();
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'product_id', 'product_id');
    }
}
