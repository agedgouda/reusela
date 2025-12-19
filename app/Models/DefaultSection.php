<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DefaultSection extends Model
{
    protected $fillable = ['section_title_id', 'text'];

    public function sectionTitle()
    {
        return $this->belongsTo(SectionTitle::class);
    }
}
