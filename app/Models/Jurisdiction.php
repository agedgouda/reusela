<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class Jurisdiction extends Model
{
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

    public function county()
    {
        return $this->belongsTo(County::class, 'county_id');
    }

    public function sections()
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
    public function getDefaultRecord()
    {
        return Cache::tags(['jurisdictions'])->rememberForever('jurisdiction_master_record', function() {
            $record = self::withoutGlobalScope('excludeDefault')
                ->where('is_system_default', true)
                ->first();
            return $record;
        });
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
    public function getDisplayGeneralInfoAttribute()
    {
        if (!empty($this->general_information)) {
            return $this->general_information;
        }

        $master = $this->getDefaultRecord();
        return $master ? $master->general_information : '';
    }

    /**
     * Accessor: $jurisdiction->display_sections
     * Returns local sections if they exist, otherwise returns master sections.
     * All sections are automatically sorted by the Title's sort_order.
     */
    public function getDisplaySectionsAttribute()
    {
        if ($this->sections()->exists()) {
            return $this->sections()
                ->with('sectionTitle')
                ->get()
                ->sortBy(fn($s) => $s->sectionTitle->sort_order ?? 0);
        }

        // Wrap this in tags so the flush command can find it
        return Cache::tags(['jurisdictions'])->rememberForever('master_sections_collection', function() {
            $master = $this->getDefaultRecord();

            return $master
                ? $master->sections()
                    ->with('sectionTitle')
                    ->get()
                    ->sortBy(fn($s) => $s->sectionTitle->sort_order ?? 0)
                : collect();
        });
    }
    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        foreach ($filters as $filter => $value) {
            $filterClass = '\\App\\Filters\\' . ucfirst($filter) . 'Filter';
            if (class_exists($filterClass) && !is_null($value)) {
                (new $filterClass)($query, $value);
            }
        }
        return $query;
    }
}
