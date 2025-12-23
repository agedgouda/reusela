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
        // 1. Automatically hide the System Default from all searches/lists
        static::addGlobalScope('excludeDefault', function (Builder $builder) {
            $builder->where('is_system_default', false);
        });

        // 2. Clear caches whenever ANY record is updated
        static::saved(function ($jurisdiction) {
            // Clear the specific search cache for THIS city name
            Cache::forget("jurisdiction_model_{$jurisdiction->name}");

            // If this is the Master record, clear the global fallback caches
            if ($jurisdiction->is_system_default) {
                Cache::forget('jurisdiction_master_record');
                Cache::forget('master_general_info');
                Cache::forget('master_sections_collection');
            }
        });

        // 3. Clear cache on delete to prevent "Ghost" results
        static::deleted(function ($jurisdiction) {
            Cache::forget("jurisdiction_model_{$jurisdiction->name}");
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
        return Cache::rememberForever('jurisdiction_master_record', function() {
            return self::withoutGlobalScope('excludeDefault')
                ->where('is_system_default', true)
                ->first();
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
        // Check for local sections first
        if ($this->sections()->exists()) {
            return $this->sections()
                ->with('sectionTitle')
                ->get()
                ->sortBy(fn($s) => $s->sectionTitle->sort_order ?? 0);
        }

        // Fallback to Master sections
        return Cache::rememberForever('master_sections_collection', function() {
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
