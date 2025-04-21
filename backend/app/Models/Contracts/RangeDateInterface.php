<?php

namespace App\Models\Contracts;

interface RangeDateInterface
{
    public function scopeWhenRangeDateFilter($query, $params);
}
