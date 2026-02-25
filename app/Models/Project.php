<?php

namespace App\Models;

use App\Helpers\UploadHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $table = 'projects';

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
        return $this->hasMany(ProjectImage::class)->orderBy('order');
    }

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class)->orderBy('floor', 'desc')->orderBy('column');
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

        static::deleting(function ($project) {
            if ($project->main_image) {
                UploadHelper::deleteFile($project->main_image);
            }
            if ($project->layout_image) {
                UploadHelper::deleteFile($project->layout_image);
            }
        });
    }
}

