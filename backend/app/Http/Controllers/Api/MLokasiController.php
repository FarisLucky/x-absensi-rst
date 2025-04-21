<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ApiResponse;
use App\Http\Requests\StoreMLokasiRequest;
use App\Http\Requests\UpdateMLokasiRequest;
use App\Models\MLokasi;
use Exception;

class MLokasiController extends Controller
{
    use ApiResponse;

    public function index()
    {
        try {

            $lokasis = MLokasi::selectIdx()->get();

            return $this->okApiResponse(
                $lokasis,
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

            $lokasis = MLokasi::selectIdx()->where('status', $status)->get();
            if ($lokasis->isEmpty()) {
                throw new Exception('Lokasi tidak ada yang aktif');
            }

            return $this->okApiResponse(
                $lokasis,
                'Berhasil Memuat Data'
            );
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function store(StoreMLokasiRequest $request)
    {

        try {
            $input = $request->validated();

            $lokasi = MLokasi::create($input);

            return $this->okApiResponse($lokasi, 'Berhasil disimpan');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function show($id)
    {
        try {

            $lokasi = MLokasi::find($id);

            return $this->okApiResponse($lokasi, 'Berhasil dimuat');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function update(UpdateMLokasiRequest $request, $id)
    {
        try {

            $input = $request->validated();
            $lokasi = MLokasi::find($id);

            $lokasi->update($input);

            return $this->okApiResponse($lokasi, 'Berhasil diperbarui');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function destroy($id)
    {
        try {

            $lokasi = MLokasi::find($id);
            $lokasi->delete();

            return $this->noContentApiResponse('');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }
}
