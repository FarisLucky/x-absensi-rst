<?php

namespace App\Services;

use App\Models\Lembur;

class LemburService
{
    public function findByDateRangeAndUnit(array $params)
    {
        $lemburs = Lembur::with([
            'mUnit' => function ($query) {
                $query->select('id', 'nama');
            },
            'acc1By' => function ($query) {
                $query->select('nip', 'nama');
            },
            'acc2By' => function ($query) {
                $query->select('nip', 'nama');
            },
        ])
            ->selectIdx()
            ->whereBetween('tanggal', [$params['start'], $params['end']])
            ->when(!is_null($params['id_unit']), function ($query) use ($params) {
                $query->where('id_unit', $params['id_unit']);
            })
            // ->whereNotNull('acc_status')
            ->orderBy('tanggal')
            ->orderBy('id_unit')
            ->orderBy('nama')
            ->get();

        return $lemburs;
    }

    public function findByDateRange(array $params)
    {
        $lemburs = Lembur::with([
            'mUnit' => function ($query) {
                $query->select('id', 'nama');
            },
            'acc1By' => function ($query) {
                $query->select('nip', 'nama');
            },
            'acc2By' => function ($query) {
                $query->select('nip', 'nama');
            },
        ])
            ->selectIdx()
            // ->whereBetween('tanggal', [$params['start'], $params['end']])
            // ->when(!is_null($params['id_unit']), function ($query) use ($params) {
            //     $query->where('id_unit', $params['id_unit']);
            // })
            ->orderBy('lembur')
            ->orderBy('nama')
            ->get();

        return $lemburs;
    }
}
