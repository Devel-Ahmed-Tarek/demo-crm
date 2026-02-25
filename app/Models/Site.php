<?php

namespace App\Models;

use App\Helpers\UploadHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Site extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'image',
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute(): ?string
    {
        return UploadHelper::url($this->image);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($site) {
            if ($site->image) {
                UploadHelper::deleteFile($site->image);
            }
        });
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function currentProjects(): HasMany
    {
        return $this->hasMany(Project::class)->where('type', 'current');
    }

    public function previousProjects(): HasMany
    {
        return $this->hasMany(Project::class)->where('type', 'previous');
    }
}

