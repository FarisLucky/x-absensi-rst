<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ApiResponse;
use App\Http\Requests\StoreIzinRequest;
use App\Models\Izin;
use App\Models\IzinBukti;
use App\Models\IzinDetail;
use App\Models\Jadwal;
use App\Models\MKaryawan;
use App\Models\User;
use App\Services\FileUploadService;
use App\Services\IzinService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

class IzinController extends Controller
{
    use ApiResponse;

    private IzinService $izinService;

    public function __construct(IzinService $izinService)
    {
        $this->izinService = $izinService;
    }

    public function selesai()
    {
        try {

            $user = auth()->user();
            $month = request('month');
            $year = request('year');
            $search = request('search');
            $izin = request('izin');

            $izins = Izin::with([
                'pemohon' => function ($query) {
                    $query->select('nip', 'id_unit', 'unit', 'photo');
                },
                'izinBukti',
            ])
                ->selectIdx()
                ->where('created_by', $user->nip)
                ->whereIn('acc_status', [Izin::SELESAI, IZIN::DITOLAK])
                ->when(!is_null($month) && !is_null($year), function ($query) use ($month, $year) {
                    $query->whereMonth('mulai', $month)
                        ->whereYear('mulai', $year);
                })
                ->when(!is_null($search), function ($query) use ($search) {
                    $query->where('izin', 'LIKE', "%{$search}%");
                })
                ->when(!is_null($izin), function ($query) use ($izin) {
                    $query->where('idm_izin', $izin);
                })
                ->latest()
                ->get();

            return $this->okApiResponse(
                $izins,
                'Berhasil Memuat Data'
            );
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function progressByNip()
    {
        try {
            $user = auth()->user();

            $izins = Izin::with([
                'pemohon' => function ($query) {
                    $query->select('nip', 'id_unit', 'unit', 'photo');
                },
                'izinBukti',
            ])
                ->selectIdx()
                ->where('created_by', $user->nip)
                ->whereIn('acc_status', [Izin::PENGAJUAN, Izin::PROGRESS])
                ->latest()
                ->get();

            return $this->okApiResponse(
                $izins,
                'Berhasil Memuat Data'
            );
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function confirmByNip()
    {
        try {

            $user = auth()->user();

            $izins = [];
            if ($user->role === User::SUPER_ADMIN) {

                $izins = Izin::with([
                    'pemohon' => function ($query) {
                        $query->select('nip', 'id_unit', 'unit', 'photo');
                    },
                    'izinBukti',
                ])
                    ->selectIdx()
                    ->where(function ($query) {
                        $query->where('acc_status', 0);
                    })
                    ->orderByDesc('created_at')
                    ->get();
            }

            return $this->okApiResponse(
                $izins,
                'Berhasil Memuat Data'
            );
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $izin = Izin::with([
                'pemohon' => function ($query) {
                    $query->select('nip', 'photo');
                },
                'izinBukti',
            ])
                ->selectIdx()
                ->where('id', $id)
                ->first();

            return $this->okApiResponse($izin, ' Berhasil dimuat');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function store(StoreIzinRequest $request)
    {
        try {
            $params = [
                'request' => $request->all(),
                'user' => auth()->user(),
            ];

            $izin = $this->izinService->izinCuti($params);

            if (!is_null($request->bukti)) {
                $validator = Validator::make($request->all(), [
                    'bukti' => 'required|mimes:jpg,jpeg,png|max:7096',
                ], [
                    'mimes' => 'Upload berupa gambar',
                    'max'   => 'Maksimal 7 MB'
                ]);
                if ($validator->fails()) {
                    throw new Exception('Upload berupa gambar dan Maksimal 7 MB');
                }
                $file = $request->file('bukti');
                /**
                 * RUN UPLOAD
                 */
                $uploadService = new FileUploadService(
                    $file,
                    Uuid::uuid4()->toString() . '.' . $file->getClientOriginalExtension(),
                    'public',
                    'izin',
                    $file->getClientOriginalExtension(),
                );
                $uploadService
                    ->setIzin($izin)
                    ->upload();
            }

            return $this->okApiResponse('OK', 'Berhasil dibuat');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function lastIzin($nip)
    {
        try {

            $izins = Izin::select('id', 'nip', 'izin', 'mulai', 'akhir', 'acc_status')
                ->where('nip', $nip)
                ->latest()
                ->limit(5)
                ->get();

            return $this->okApiResponse($izins, ' Berhasil dimuat');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function accSubmit(Request $request)
    {
        try {
            $params = [
                'request' => $request->all(),
                'user' => auth()->user(),
            ];

            $this->izinService->accSubmit($params);

            return $this->okApiResponse('OK', ' Berhasil dimuat');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
            // return $this->errorApiResponse($th->getTraceAsString());
        }
    }

    public function destroy($id)
    {
        try {

            $izin = Izin::find($id);
            if (!is_null($izin->acc1_at)) {
                throw new \Exception('Sudah di acc pihak 1');
            }
            $izin->delete();

            return $this->okApiResponse(
                'OK',
                'Berhasil Menghapus Data'
            );
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function bukti($id)
    {
        $bukti = IzinBukti::where('id_izin', $id)->first();

        if (is_null($bukti)) {
            abort(404);
        }

        $filename = $bukti->path . '/' . $bukti->nama;
        $file = Storage::disk($bukti->disk)->path($filename);

        return response()->file($file);
    }

    public function batal(Request $request, $id)
    {
        try {

            DB::beginTransaction();
            $izin = Izin::findOrFail($id);
            $izinDetail = IzinDetail::where('id_izin', $izin->id)->get();
            if (count($izinDetail) > 0) {
                foreach ($izinDetail as $detail) {
                    $jadwal = Jadwal::where(function ($query) use ($detail) {
                        $query->where('tanggal', $detail->tanggal)
                            ->where('nip', $detail->nip);
                    })->first();

                    if (!is_null($jadwal)) {
                        $jadwal->status = null;
                        $jadwal->save();
                    }
                }
            }

            $karyawan = MKaryawan::where('nip', $izin->nip)->firstOrFail();
            $karyawan->jml_cuti = intval($izin->periode) + intval($izin->sisa);
            $karyawan->save();

            if ($request->ket !== '' || $request->ket !== '') {
                $izin->ket = "{$izin->ket} (ditolak: {$request->ket})";
            }

            $izin->acc_status = Izin::BATAL;
            $izin->save();

            DB::commit();
            return $this->okApiResponse(
                $izin,
                'Berhasil Menghapus Data'
            );
        } catch (\Throwable $th) {
            DB::rollBack();

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function needApproval()
    {
        try {

            $izin = Izin::where('acc_status', Izin::PENGAJUAN)->count();

            return $this->okApiResponse($izin);
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }
}
