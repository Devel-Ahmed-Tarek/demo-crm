<?php

namespace App\Models;

use App\Helpers\UploadHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BuildingImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'building_id',
        'image_path',
        'order',
    ];

    protected $appends = ['image_url'];

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        return UploadHelper::url($this->image_path);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($image) {
            UploadHelper::deleteFile($image->image_path);
        });
    }
}

