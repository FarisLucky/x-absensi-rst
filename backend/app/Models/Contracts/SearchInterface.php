<?php

namespace App\Models\Contracts;

interface SearchInterface
{
    public function scopeWhenSearchFilter($query, $params);
}
