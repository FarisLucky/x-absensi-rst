<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ApiResponse;
use App\Http\Requests\StoreMKaryawanDokRequest;
use App\Models\MKaryawanDok;
use App\Services\FileUploadService;
use Exception;
use Illuminate\Support\Facades\Validator;

class MKaryawanDokController extends Controller
{
    use ApiResponse;

    public function index()
    {
        try {
            $nip = request('nip');
            $karyawanDoks = MKaryawanDok::selectIdx()
                ->when(!is_null($nip), function ($query) use ($nip) {
                    $query->where('nip', $nip);
                })
                ->latest()
                ->get();

            return $this->okApiResponse(
                $karyawanDoks,
                'Berhasil Memuat Data'
            );
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function data()
    {
        try {

            $karyawanDoks = MKaryawanDok::selectIdx()->get();

            return $this->okApiResponse(
                $karyawanDoks,
                'Berhasil Memuat Data'
            );
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function store(StoreMKaryawanDokRequest $request)
    {

        try {
            $input = $request->validated();
            if (!is_null($request->file)) {
                $validator = Validator::make($request->all(), [
                    'file' => 'required|mimes:jpg,jpeg,png,pdf|max:7096',
                ], [
                    'mimes' => 'Upload berupa gambar',
                    'max'   => 'Maksimal 7 MB'
                ]);
                if ($validator->fails()) {
                    throw new Exception('Upload berupa gambar dan Maksimal 7 MB');
                }
                $file = $request->file('file');
            }
            $karyawanDok = MKaryawanDok::create($input);

            return $this->okApiResponse($karyawanDok, 'Berhasil disimpan');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function show($id)
    {
        try {

            $karyawanDok = MKaryawanDok::find($id);

            return $this->okApiResponse($karyawanDok, 'Berhasil dimuat');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function update(UpdateMKaryawanDokRequest $request, $id)
    {
        try {

            $karyawanDok = MKaryawanDok::find($id);

            $input = $request->validated();

            $karyawanDok->update($input);

            return $this->okApiResponse($karyawanDok, 'Berhasil diperbarui');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function destroy($id)
    {
        try {

            $karyawanDok = MKaryawanDok::find($id);
            $karyawanDok->delete();

            return $this->noContentApiResponse('');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }
}
