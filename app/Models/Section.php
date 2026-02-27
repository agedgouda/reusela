<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;

class Section extends Model
{
    use HasFactory;

    // This triggers the 'saved' event on the parent Jurisdiction
    protected $touches = ['jurisdiction'];

    protected $fillable = [
        'jurisdiction_id',
        'section_title_id',
        'text',
    ];

    protected static function booted()
    {
        static::saved(function ($section) {
            // If this section belongs to a jurisdiction, flush the tags
            // This ensures the 'master_sections_collection' is refreshed
            Cache::tags(['jurisdictions'])->flush();
        });

        static::deleted(function ($section) {
            Cache::tags(['jurisdictions'])->flush();
        });
    }

    public function jurisdiction()
    {
        return $this->belongsTo(Jurisdiction::class, 'jurisdiction_id', 'id');
    }

    public function sectionTitle()
    {
        return $this->belongsTo(SectionTitle::class);
    }
}
