<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Section extends Model
{
    use HasFactory;

    protected $fillable = [
        'jurisdiction_id',
        'section_title_id',
        'text',
    ];

    public function jurisdiction()
    {
        return $this->belongsTo(Jurisdiction::class, 'jurisdiction_id', 'id');
    }

    public function sectionTitle()
    {
        return $this->belongsTo(SectionTitle::class);
    }
}
