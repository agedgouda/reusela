<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class Jurisdiction extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * The primary key type is string, and it is not auto-incrementing.
     */
    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'county_id',
        'name',
        'general_information',
    ];

    /**
     * Define the relationship with the County model.
     */
    public function county()
    {
        return $this->belongsTo(County::class, 'county_id');
    }

    /**
     * Define the relationship with the Section model.
     * One jurisdiction has many sections.
     */
    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    /**
     * Filter scope.
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
