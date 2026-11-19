<?php

namespace App\Models;

use App\Casts\SanitizedHtml;
use Database\Factories\JurisdictionFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class Jurisdiction extends Model
{
    /** @use HasFactory<JurisdictionFactory> */
    use HasFactory, HasUuids;

    /**
     * Set the primary key type to UUID string.
     */
    protected $keyType = 'string';

    public $incrementing = false;

    /**
     * Attributes that are mass assignable.
     * Ensure is_system_default is here so you can set it.
     */
    protected $fillable = [
        'id',
        'county_id',
        'name',
        'general_information',
        'is_system_default',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'general_information' => SanitizedHtml::class,
        ];
    }

    /**
     * The "booted" method handles Global Scopes and Model Events.
     */
    protected static function booted()
    {
        static::addGlobalScope('excludeDefault', function (Builder $builder) {
            $builder->where('is_system_default', false);
        });

        static::saved(function ($jurisdiction) {
            // This clears EVERYTHING tagged 'jurisdictions' instantly
            Cache::tags(['jurisdictions'])->flush();
        });

        static::deleted(function ($jurisdiction) {
            Cache::tags(['jurisdictions'])->flush();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * @return BelongsTo<County, $this>
     */
    public function county(): BelongsTo
    {
        return $this->belongsTo(County::class, 'county_id');
    }

    /**
     * @return HasMany<Section, $this>
     */
    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Fetches the hidden Master record by bypassing the Global Scope.
     */
    public function getDefaultRecord(): ?self
    {
        /*
         * Only the raw attributes are cached, not the model, because Laravel 13
         * no longer unserializes objects from the cache by default.
         */
        $attributes = Cache::tags(['jurisdictions'])->rememberForever('jurisdiction_master_record_attributes', function () {
            return self::withoutGlobalScope('excludeDefault')
                ->where('is_system_default', true)
                ->first()
                ?->getAttributes();
        });

        return is_array($attributes) ? (new self)->newFromBuilder($attributes) : null;
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors (The Waterfall Logic)
    |--------------------------------------------------------------------------
    */

    /**
     * Accessor: $jurisdiction->display_general_info
     * Returns local content if available, otherwise returns the master default.
     */
    public function getDisplayGeneralInfoAttribute(): string
    {
        if (! empty($this->general_information)) {
            return $this->general_information;
        }

        return $this->getDefaultRecord()->general_information ?? '';
    }

    /**
     * Accessor: $jurisdiction->display_sections
     * Returns local sections if they exist, otherwise returns master sections.
     * All sections are automatically sorted by the Title's sort_order.
     */
    /**
     * @return Collection<int, Section>
     */
    public function getDisplaySectionsAttribute(): Collection
    {
        if ($this->sections()->exists()) {
            return $this->sortedSections($this);
        }

        // Tagged so the flush on save can find it. Rows of raw attributes are cached
        // rather than models, because Laravel 13 no longer unserializes cached objects.
        $rows = Cache::tags(['jurisdictions'])->rememberForever('master_section_rows', function (): array {
            $master = $this->getDefaultRecord();

            if (! $master) {
                return [];
            }

            return $this->sortedSections($master)
                ->map(fn (Section $section): array => [
                    'section' => $section->getAttributes(),
                    'section_title' => $section->sectionTitle?->getAttributes(),
                ])
                ->values()
                ->all();
        });

        return new EloquentCollection(array_map(
            fn (array $row): Section => $this->sectionFromCachedRow($row),
            $rows,
        ));
    }

    /**
     * @param  array{section: array<string, mixed>, section_title: array<string, mixed>|null}  $row
     */
    private function sectionFromCachedRow(array $row): Section
    {
        $section = (new Section)->newFromBuilder($row['section']);

        $section->setRelation(
            'sectionTitle',
            $row['section_title'] === null ? null : (new SectionTitle)->newFromBuilder($row['section_title']),
        );

        return $section;
    }

    /**
     * @return Collection<int, Section>
     */
    private function sortedSections(self $jurisdiction): Collection
    {
        return $jurisdiction->sections()
            ->with('sectionTitle')
            ->get()
            ->sortBy(fn (Section $section) => $section->sectionTitle->sort_order ?? 0);
    }
}
