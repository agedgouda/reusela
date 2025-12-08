<?php

namespace App\Filters;

use Closure;

class NameFilter
{
    protected $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function handle($query, Closure $next)
    {
        if (!is_null($this->value)) {
            $query->where('name', 'like', '%' . $this->value . '%');
        }

        return $next($query);
    }
}
