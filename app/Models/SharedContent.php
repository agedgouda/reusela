<?php

namespace App\Models;

use App\Casts\SanitizedHtml;
use Illuminate\Database\Eloquent\Model;

class SharedContent extends Model
{
    protected $fillable = ['key', 'content'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'content' => SanitizedHtml::class,
        ];
    }
}
