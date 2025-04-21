<?php

namespace App\Models\Contracts;

interface JenisInterface
{
    public function scopeWhenJenisFilter($query, $params);
}
