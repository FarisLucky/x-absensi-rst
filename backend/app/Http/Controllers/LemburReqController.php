<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\ApiResponse;
use App\Http\Requests\StoreLemburRequest;
use App\Http\Requests\UpdateLemburRequest;
use App\Models\LemburApprov;
use App\Models\LemburReq;
use App\Models\MKaryawan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class LemburReqController extends Controller
{
    use ApiResponse;

    public function index()
    {
        try {

            $perPage = request('perPage');
            $month = request('month');
            $search = request('search');
            $user = auth()->user();
            $lemburs = LemburReq::leftJoin('m_karyawan', 'm_karyawan.nip', '=', 'lembur_req.nip')
                ->with([
                    'lemburApprov' => function ($query) {
                        $query->selectIdx();
                    }
                ])
                ->select([
                    'lembur_req.id',
                    'lembur_req.nip',
                    'lembur_req.nama',
                    'lembur_req.unit',
                    'lembur_req.tanggal',
                    'lembur_req.mulai',
                    'lembur_req.akhir',
                    'lembur_req.ttl_jam',
                    'lembur_req.ttl_menit',
                    'lembur_req.status',
                    'lembur_req.catatan',
                    'lembur_req.created_at',
                    'm_karyawan.photo',
                ])
                ->when(!is_null($month), function ($query) use ($month) {
                    $explode = explode('-', $month);
                    $query->whereMonth('tanggal', $explode[1])->whereYear('tanggal', $explode[0]);
                })
                ->when(!is_null($search), function ($query) use ($search) {
                    $query->where('lembur_req.nip', 'LIKE', "%{$search}%")
                        ->orWhere('lembur_req.nama', 'LIKE', "%{$search}%");
                })
                ->when($user->role !== User::SUPER_ADMIN, function ($query) use ($user) {
                    $query->where('lembur_req.nip', $user->nip);
                })
                ->latest()
                ->paginate($perPage);

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

            $lemburs = LemburReq::select(['id', 'kode', 'nama', 'acc_manajemen', 'tahunan'])->get();

            return $this->okApiResponse(
                $lemburs,
                'Berhasil Memuat Data'
            );
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function store(StoreLemburRequest $request)
    {

        try {
            $user = auth()->user();
            $mulai = Carbon::createFromFormat('Y-m-d H:i', $request->mulai);
            $akhir = Carbon::createFromFormat('Y-m-d H:i', $request->akhir);
            $karyawan = MKaryawan::where('nip', $user->nip)->first();

            $input = $request->validated();
            $input['nip'] = $karyawan->nip;
            $input['nama'] = $karyawan->nama;
            $input['unit'] = $karyawan->unit;
            $input['mulai'] = $mulai->format('Y-m-d H:i:s');
            $input['akhir'] = $akhir->format('Y-m-d H:i:s');
            $input['ttl_jam'] = $mulai->diffInHours($akhir);
            $input['ttl_menit'] = $mulai->diffInMinutes($akhir);
            $input['status'] = LemburReq::PENGAJUAN;

            $lembur = LemburReq::create($input);

            return $this->okApiResponse($lembur, 'Berhasil disimpan');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function show($id)
    {
        try {

            $lembur = LemburReq::find($id);

            return $this->okApiResponse($lembur, 'Berhasil dimuat');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function update(UpdateLemburRequest $request, $id)
    {
        try {

            $input = $request->validated();
            $lembur = LemburReq::find($id);

            $lembur->update($input);

            return $this->okApiResponse($lembur, 'Berhasil diperbarui');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function updateStatus(Request $request, $id)
    {
        try {

            $karyawan = MKaryawan::where('nip', auth()->user()->nip)
                ->first([
                    'nip',
                    'nama'
                ]);

            $lembur = LemburReq::find($request->id);
            if (!is_null($lembur)) {
                if ($request->jenis === 'TERIMA') {
                    $lembur->status = LemburReq::DISETUJUI;
                } else if ($request->jenis === 'TOLAK') {
                    $lembur->status = LemburReq::DITOLAK;
                }
                $lembur->save();

                LemburApprov::create([
                    'id_lembur' => $request->id,
                    'acc_by' => $karyawan->nip,
                    'acc_nama' => $karyawan->nama,
                    'acc_at' => now(),
                    'status' => $request->jenis,
                    'ket' => $request->catatan,
                ]);
            }

            return $this->okApiResponse($lembur, 'Berhasil diperbarui');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function destroy($id)
    {
        try {

            $lembur = LemburReq::find($id);
            $lembur->delete();

            return $this->noContentApiResponse('');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function needApproval()
    {
        try {

            $lembur = LemburReq::where('status', LemburReq::PENGAJUAN)->count();

            return $this->okApiResponse($lembur);
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }
}
