<?php

namespace App\Models;

use App\Helpers\UploadHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Building extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'title',
        'description',
        'main_image',
        'layout_image',
        'type',
    ];

    protected $appends = ['main_image_url', 'layout_image_url'];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(BuildingImage::class)->orderBy('order');
    }

    public function getMainImageUrlAttribute(): ?string
    {
        return UploadHelper::url($this->main_image);
    }

    public function getLayoutImageUrlAttribute(): ?string
    {
        return UploadHelper::url($this->layout_image);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($building) {
            if ($building->main_image) {
                UploadHelper::deleteFile($building->main_image);
            }
            if ($building->layout_image) {
                UploadHelper::deleteFile($building->layout_image);
            }
        });
    }
}

