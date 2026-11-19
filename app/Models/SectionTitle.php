<?php

namespace App\Models;

use Database\Factories\SectionTitleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SectionTitle extends Model
{
    /** @use HasFactory<SectionTitleFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'sort_order',
        'icon',
    ];

    /**
     * @return HasMany<Section, $this>
     */
    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }
}
