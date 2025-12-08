<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class County extends Model
{
    use HasFactory;

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($model) {
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
    
    public function jurisdictions()
    {
        return $this->hasMany(Jurisdiction::class, 'county_id');
    }
}
