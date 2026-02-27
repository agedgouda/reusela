<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;

class Section extends Model
{
    use HasFactory;

    // Keep this; it ensures the Jurisdiction's 'updated_at' changes
    protected $touches = ['jurisdiction'];

    protected $fillable = [
        'jurisdiction_id',
        'section_title_id',
        'text',
    ];

    protected static function booted()
    {
        static::saved(function ($section) {
            \Log::info('--- SECTION SAVED START ---');
            \Log::info('Section ID: ' . $section->id);
            \Log::info('Linked Jurisdiction ID: ' . ($section->jurisdiction_id ?? 'NULL'));

            // Check the driver - if this says 'file', tags will NOT work
            $driver = config('cache.default');
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
