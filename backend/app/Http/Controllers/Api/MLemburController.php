<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ApiResponse;
use App\Http\Requests\StoreMLemburRequest;
use App\Http\Requests\UpdateMLemburRequest;
use App\Models\MLembur;

class MLemburController extends Controller
{
    use ApiResponse;

    public function index()
    {
        try {

            $lemburs = MLembur::with([
                'mLokasi' => function ($query) {
                    $query->select('id', 'nama');
                }
            ])
                ->selectIdx()
                ->get();

            return $this->okApiResponse(
                $lemburs,
                'Berhasil Memuat Data'
            );
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function data()
    {
        try {
            $status = request('status');

            $lokasis = MLembur::with('mLokasi')
                ->where('status', intval($status))
                ->get();

            return $this->okApiResponse(
                $lokasis,
                'Berhasil Memuat Data'
            );
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }
    public function store(StoreMLemburRequest $request)
    {

        try {
            $input = $request->validated();

            $lembur = MLembur::create($input);

            return $this->okApiResponse($lembur, 'Berhasil disimpan');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function show($id)
    {
        try {

            $lembur = MLembur::find($id);

            return $this->okApiResponse($lembur, 'Berhasil dimuat');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function update(UpdateMLemburRequest $request, $id)
    {
        try {

            $input = $request->validated();
            $lembur = MLembur::find($id);

            $lembur->update($input);

            return $this->okApiResponse($lembur, 'Berhasil diperbarui');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function destroy($id)
    {
        try {

            $lembur = MLembur::find($id);
            $lembur->delete();

            return $this->noContentApiResponse('');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }
}
