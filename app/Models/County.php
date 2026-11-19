<?php

namespace App\Models;

use Database\Factories\CountyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class County extends Model
{
    /** @use HasFactory<CountyFactory> */
    use HasFactory;

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function (County $model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid(); // Auto-generate UUID for primary key
            }
        });
    }

    // Specify that the primary key is a UUID
    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'name',
    ];

    /**
     * @return HasMany<Jurisdiction, $this>
     */
    public function jurisdictions(): HasMany
    {
        return $this->hasMany(Jurisdiction::class, 'county_id');
    }
}
