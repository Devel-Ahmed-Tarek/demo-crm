<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadSource extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'label_en',
        'label_ar',
        'description_en',
        'description_ar',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getLabelAttribute(): string
    {
        $locale = app()->getLocale();
        $label = $locale === 'ar' ? ($this->label_ar ?: $this->label_en) : ($this->label_en ?: $this->label_ar);
        return $label ?: ucfirst($this->key);
    }

    public function getDescriptionAttribute(): ?string
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? ($this->description_ar ?: $this->description_en) : ($this->description_en ?: $this->description_ar);
    }
}

