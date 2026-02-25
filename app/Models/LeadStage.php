<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeadStage extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'label_en',
        'label_ar',
        'description_en',
        'description_ar',
        'accent',
        'dot',
        'border',
        'card_border',
        'shadow',
        'glow',
        'category',
        'is_contract_stage',
        'sort_order',
    ];

    protected $casts = [
        'is_contract_stage' => 'boolean',
    ];

    protected $appends = ['label', 'description'];

    public function getLabelAttribute(): string
    {
        $locale = app()->getLocale();
        $value = $locale === 'ar' ? ($this->label_ar ?: $this->label_en) : ($this->label_en ?: $this->label_ar);

        return $value ?: ucfirst($this->key);
    }

    public function getDescriptionAttribute(): ?string
    {
        $locale = app()->getLocale();
        $value = $locale === 'ar' ? ($this->description_ar ?: $this->description_en) : ($this->description_en ?: $this->description_ar);

        return $value;
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class, 'stage', 'key');
    }

    public function getCategoryAttribute($value): string
    {
        return $value ?: 'positive';
    }
}
