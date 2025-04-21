<?php

namespace App\Models\Contracts;

interface UnitInterface
{
    public function scopeWhenUnitFilter($query, $params);
}
