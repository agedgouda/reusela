<?php

namespace App\Filters;

use App\Models\Jurisdiction;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class NameFilter
{
    public function __construct(protected ?string $value) {}

    /**
     * @param  Builder<Jurisdiction>  $query
     * @param  Closure(Builder<Jurisdiction>): Builder<Jurisdiction>  $next
     * @return Builder<Jurisdiction>
     */
    public function handle(Builder $query, Closure $next): Builder
    {
        if (! is_null($this->value)) {
            $query->where('name', 'like', '%'.$this->value.'%');
        }

        return $next($query);
    }
}
