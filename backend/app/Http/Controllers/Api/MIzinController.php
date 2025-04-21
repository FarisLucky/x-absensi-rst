<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ApiResponse;
use App\Http\Requests\StoreMIzinRequest;
use App\Http\Requests\UpdateMIzinRequest;
use App\Models\MIzin;
use Illuminate\Support\Facades\Log;

class MIzinController extends Controller
{
    use ApiResponse;

    public function index()
    {
        try {

            $izins = MIzin::selectIdx()->get();

            return $this->okApiResponse(
                $izins,
                'Berhasil Memuat Data'
            );
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function data()
    {
        try {

            $izins = MIzin::select(['id', 'kode', 'nama','acc_manajemen','tahunan'])->get();

            return $this->okApiResponse(
                $izins,
                'Berhasil Memuat Data'
            );
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function store(StoreMIzinRequest $request)
    {

        try {
            $input = $request->validated();

            Log::info('input', $input);
            $izin = MIzin::create($input);

            return $this->okApiResponse($izin, 'Berhasil disimpan');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function show($id)
    {
        try {

            $izin = MIzin::find($id);

            return $this->okApiResponse($izin, 'Berhasil dimuat');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function update(UpdateMIzinRequest $request, $id)
    {
        try {

            $input = $request->validated();
            $izin = MIzin::find($id);

            $izin->update($input);

            return $this->okApiResponse($izin, 'Berhasil diperbarui');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function destroy($id)
    {
        try {

            $izin = MIzin::find($id);
            $izin->delete();

            return $this->noContentApiResponse('');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function byKode($kode)
    {
        try {

            $izins = MIzin::where('kode', $kode)->selectIdx()->get();

            return $this->okApiResponse(
                $izins,
                'Berhasil Memuat Data'
            );
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }
}
