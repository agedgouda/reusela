<?php

namespace App\Models;

use App\Casts\SanitizedHtml;
use Database\Factories\SectionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class Section extends Model
{
    /** @use HasFactory<SectionFactory> */
    use HasFactory;

    /**
     * Keep this; it ensures the Jurisdiction's 'updated_at' changes.
     *
     * @var list<string>
     */
    protected $touches = ['jurisdiction'];

    protected $fillable = [
        'jurisdiction_id',
        'section_title_id',
        'text',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'text' => SanitizedHtml::class,
        ];
    }

    protected static function booted(): void
    {
        static::saved(function () {
            Cache::tags(['jurisdictions'])->flush();
        });
    }

    /**
     * @return BelongsTo<Jurisdiction, $this>
     */
    public function jurisdiction(): BelongsTo
    {
        return $this->belongsTo(Jurisdiction::class, 'jurisdiction_id', 'id');
    }

    /**
     * @return BelongsTo<SectionTitle, $this>
     */
    public function sectionTitle(): BelongsTo
    {
        return $this->belongsTo(SectionTitle::class);
    }
}
