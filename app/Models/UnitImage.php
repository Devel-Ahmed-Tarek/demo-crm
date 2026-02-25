<?php

namespace App\Models;

use App\Helpers\UploadHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnitImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_id',
        'image_path',
        'is_primary',
        'order',
    ];

    protected $appends = ['image_url'];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Get the image URL attribute
     */
    public function getImageUrlAttribute(): ?string
    {
        return UploadHelper::url($this->image_path);
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Delete file when model is deleted
        static::deleting(function ($image) {
            UploadHelper::deleteFile($image->image_path);
        });
    }
}
