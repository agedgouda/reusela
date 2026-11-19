<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DefaultSection extends Model
{
    protected $fillable = ['section_title_id', 'text'];

    /**
     * @return BelongsTo<SectionTitle, $this>
     */
    public function sectionTitle(): BelongsTo
    {
        return $this->belongsTo(SectionTitle::class);
    }
}
