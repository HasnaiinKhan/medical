<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Medicine extends Model
{
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'manufacturer',
        'description',
        'mrp_paise',
        'price_paise',
        'prescription_required',
        'stock',
        'image_url',
        'extra_images',
    ];

    /**
     * Returns the product image URL, falling back to a deterministic
     * placeholder based on the medicine's primary key.
     */
    public function imageUrl(): string
    {
        if ($this->image_url) {
            return $this->image_url;
        }

        return 'https://picsum.photos/seed/med-' . $this->id . '/400/400';
    }

    /** All images: primary + extras */
    public function allImages(): array
    {
        $primary = [$this->imageUrl()];
        $extras  = array_filter((array) ($this->extra_images ?? []));
        return array_values(array_merge($primary, $extras));
    }

    protected function casts(): array
    {
        return [
            'prescription_required' => 'boolean',
            'extra_images'          => 'array',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function mrpRupees(): float
    {
        return round($this->mrp_paise / 100, 2);
    }

    public function priceRupees(): float
    {
        return round($this->price_paise / 100, 2);
    }

    public function discountPercent(): int
    {
        if ($this->mrp_paise <= 0) {
            return 0;
        }

        return (int) round(100 - ($this->price_paise / $this->mrp_paise) * 100);
    }
}
