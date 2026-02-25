<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class UnitFeature extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function units(): BelongsToMany
    {
        return $this->belongsToMany(Unit::class, 'unit_feature_pivot');
    }
}
