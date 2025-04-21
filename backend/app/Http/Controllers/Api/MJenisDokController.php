<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MJenisDokController extends Controller
{
    use ApiResponse;

    public function index()
    {
        try {

            $masdoks = DB::table('m_jenisdok')->orderBydesc('id')->get();

            return $this->okApiResponse(
                $masdoks,
                'Berhasil Memuat Data'
            );
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function data()
    {
        try {

            $masdoks = DB::table('m_jenisdok')->whereHas('jadwal')->get();

            return $this->okApiResponse(
                $masdoks,
                'Berhasil Memuat Data'
            );
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function store(Request $request)
    {

        try {
            $input = ['nama' => $request->nama];

            $masdok = DB::table('m_jenisdok')->insert($input);

            return $this->okApiResponse($masdok, 'Berhasil disimpan');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function show($id)
    {
        try {

            $masdok = DB::table('m_jenisdok')->where('id', $id)->first();

            return $this->okApiResponse($masdok, 'Berhasil dimuat');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {

            $input = ['nama' => $request->nama];
            $masdok = DB::table('m_jenisdok')->where('id', $id)->first();

            $masdok->update($input);

            return $this->okApiResponse($masdok, 'Berhasil diperbarui');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function destroy($id)
    {
        try {

            $masdok = DB::table('m_jenisdok')->where('id', '=', $id)->delete();

            return $this->noContentApiResponse($masdok);
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }
}
