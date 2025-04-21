<?php

namespace App\Models\Traits;

trait FilterSeriazable
{

    public function serializeRequestFilter($request)
    {
        if (isset($request['column_key'])) {
            foreach ($request['column_key'] as $key => $column) {
                $request[$column] = trim($request['column_val'][$key]);
            }
        }
        return $request;
    }

    public function scopeSortByFilter($query, $column, $type)
    {
        if (isset($column)) {
            $query->orderBy($column, $type);
        }
    }
}
