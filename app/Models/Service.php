<?php

namespace App\Models;

use App\Helpers\UploadHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'title_en',
        'title_ar',
        'description',
        'description_en',
        'description_ar',
        'icon',
        'icon_type',
        'icon_image',
        'link',
        'link_text',
        'link_text_en',
        'link_text_ar',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function getIconImageUrlAttribute(): ?string
    {
        if ($this->icon_type === 'image' && $this->icon_image) {
            return UploadHelper::url($this->icon_image);
        }
        return null;
    }

    public function getTranslatedTitleAttribute(): string
    {
        $locale = app()->getLocale();
        if ($locale === 'ar' && $this->title_ar) {
            return $this->title_ar;
        }
        if ($locale === 'en' && $this->title_en) {
            return $this->title_en;
        }
        return $this->title;
    }

    public function getTranslatedDescriptionAttribute(): string
    {
        $locale = app()->getLocale();
        if ($locale === 'ar' && $this->description_ar) {
            return $this->description_ar;
        }
        if ($locale === 'en' && $this->description_en) {
            return $this->description_en;
        }
        return $this->description;
    }

    public function getTranslatedLinkTextAttribute(): ?string
    {
        $locale = app()->getLocale();
        if ($locale === 'ar' && $this->link_text_ar) {
            return $this->link_text_ar;
        }
        if ($locale === 'en' && $this->link_text_en) {
            return $this->link_text_en;
        }
        return $this->link_text;
    }
}
