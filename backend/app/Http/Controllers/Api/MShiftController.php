<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ApiResponse;
use App\Http\Requests\StoreMShiftRequest;
use App\Http\Requests\UpdateMShiftRequest;
use App\Models\MShift;

class MShiftController extends Controller
{
    use ApiResponse;

    public function index()
    {
        try {

            $shifts = MShift::selectIdx()->get();

            return $this->okApiResponse(
                $shifts,
                'Berhasil Memuat Data'
            );
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }
    public function data()
    {
        try {

            $shifts = MShift::select('kode', 'nama', 'jam_masuk', 'jam_pulang')->get();
            $shifts->transform(function ($shift) {
                $shift->nama = "{$shift->nama} ({$shift->jam_masuk} - {$shift->jam_pulang})";

                return $shift;
            });

            return $this->okApiResponse(
                $shifts,
                'Berhasil Memuat Data'
            );
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function store(StoreMShiftRequest $request)
    {

        try {
            $input = $request->validated();

            $shift = MShift::create($input);

            return $this->okApiResponse($shift, 'Berhasil disimpan');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function show($id)
    {
        try {

            $shift = MShift::find($id);

            return $this->okApiResponse($shift, 'Berhasil dimuat');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function update(UpdateMShiftRequest $request, $id)
    {
        try {

            $input = $request->validated();
            $shift = MShift::find($id);

            $shift->update($input);

            return $this->okApiResponse($shift, 'Berhasil diperbarui');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function destroy($id)
    {
        try {

            MShift::where('id',$id)->delete();

            return $this->noContentApiResponse('');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }
}
