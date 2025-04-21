<?php

namespace App\Http\Controllers\Api;

use App\Exports\MKaryawanExport;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ApiResponse;
use App\Http\Requests\StoreMKaryawanRequest;
use App\Http\Requests\UpdateMKaryawanRequest;
use App\Models\Jabatan;
use App\Models\MKaryawan;
use App\Models\MKaryawanDetail;
use App\Models\MUnit;
use App\Models\ResetDevice;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class MKaryawanController extends Controller
{
    use ApiResponse;

    public function index()
    {
        try {
            $unit = request('unit');
            $search = request('search');
            $resign = request('resign');
            $perPage = request('perPage');


            $karyawans = MKaryawan::with([
                'user' => function ($query) {
                    $query->select('nip', 'role');
                },
            ])
                ->selectIdx()
                ->orderByDesc('created_at')
                ->when($search !== null, function ($query) use ($search) {
                    $query->where('nama', 'LIKE', "%{$search}%")
                        ->orWhere('nip', 'LIKE', "%{$search}%");
                })
                ->when($unit !== null, function ($query) use ($unit) {
                    $query->where('id_unit', $unit);
                })
                ->when($resign !== null, function ($query) use ($resign) {
                    if ($resign === 'AKTIF') {
                        $query->whereNull('tgl_resign');
                    } else {
                        $query->whereNotNull('tgl_resign');
                    }
                })
                ->paginate($perPage);

            return $this->okApiResponse(
                $karyawans,
                'Berhasil Memuat Data'
            );
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function data()
    {
        try {

            $q = request('q');
            $karyawans = MKaryawan::with([
                'mUnit' => function ($query) {
                    $query->selectIdx();
                },
            ])
                ->select('nip', 'nama', 'id_unit')
                ->when(!is_null($q), function ($query) use ($q) {
                    $query->whereLike('nama', $q);
                })
                ->orderByDesc('id')
                ->limit(5)
                ->get();

            return $this->okApiResponse(
                $karyawans,
                'Berhasil Memuat Data'
            );
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function store(StoreMKaryawanRequest $request)
    {

        try {
            DB::beginTransaction();

            $unit = MUnit::where('id', $request->id_unit)->first();
            $input = $request->validated();
            $input['unit'] = $unit->nama;
            if (!is_null($request->rt_rw)) {
                $explode = explode('/', $request->rt_rw);
                $rt = $explode[0];
                $rw = $explode[1];
                $input['rt'] = $rt;
                $input['rw'] = $rw;
            }

            MKaryawan::create($input);

            DB::commit();

            return $this->okApiResponse('OK', 'Berhasil disimpan');
        } catch (\Throwable $th) {

            DB::rollBack();
            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function show($nip)
    {
        try {

            $karyawan = MKaryawan::where('nip', $nip)->first();

            return $this->okApiResponse($karyawan, 'Berhasil dimuat');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function update(UpdateMKaryawanRequest $request, $id)
    {
        try {

            DB::beginTransaction();

            $input = $request->validated();

            $unit = MUnit::where('id', $request->id_unit)->first();

            $input = $request->validated();
            $input['unit'] = $unit->nama;
            if (!is_null($request->rt_rw)) {
                $explode = explode('/', $request->rt_rw);
                $rt = $explode[0];
                $rw = $explode[1];
                $input['rt'] = $rt;
                $input['rw'] = $rw;
            }
            $karyawan = MKaryawan::find($id);
            $karyawan->update($input);

            DB::commit();

            return $this->okApiResponse($karyawan, 'Berhasil diperbarui');
        } catch (\Throwable $th) {
            DB::rollBack();

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function destroy($id)
    {
        try {

            $karyawan = MKaryawan::find($id);
            // $karyawan->mKaryawanDetail->delete();
            $karyawan->delete();

            return $this->noContentApiResponse('');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function createUser(Request $request)
    {
        try {
            $karyawan = $request->karyawan;
            $password = bcrypt($request->password);
            $superAdmin = $request->role;
            if ($superAdmin === 'YA') {
                $superAdmin = 'SUPER_ADMIN';
            }
            $payload = [];
            $now = now();
            foreach ($karyawan as $k) {
                $karyawan = MKaryawan::where('nip', $k['nip'])->first();
                if (is_null($karyawan->user)) {
                    $payload[] = [
                        "nip" => $k['nip'],
                        "nama" => $k['nama'],
                        "password" => $password,
                        "role" => $superAdmin === User::SUPER_ADMIN ? User::SUPER_ADMIN : 'STAF',
                        "created_at" => $now,
                        "updated_at" => $now,
                    ];
                    continue;
                }

                $karyawan->user()->update([
                    "password" => $password,
                    "role" => $superAdmin === User::SUPER_ADMIN ? User::SUPER_ADMIN : 'STAF',
                ]);
            }

            User::insert($payload);

            return $this->okApiResponse('User created', 'Berhasil ditambahkan');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function byUnit($idUnit)
    {
        try {

            $karyawan = MKaryawan::where('id_unit', $idUnit)->get();
            return $this->okApiResponse($karyawan, 'Berhasil ditambahkan');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function sisaCuti()
    {
        try {

            $karyawan = auth()->user()->mKaryawan()->pluck('jml_cuti')->first();

            return $this->okApiResponse($karyawan, 'Berhasil ditambahkan');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function updateCuti(Request $request, $nip)
    {
        try {

            $karyawan = MKaryawan::where('nip', $nip)->first();
            $karyawan->update([
                'cuti' => $request->cuti
            ]);

            return $this->okApiResponse('OK', 'Berhasil ditambahkan');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function updateSession($nip)
    {
        try {

            $karyawan = MKaryawan::with('jabatans', 'jabatans.mJabatan')->where('nip', $nip)->first();
            $user = User::where('nip', $nip)->first();
            if ($karyawan->jabatans->isNotEmpty()) {
                $user->update([
                    'role' => $karyawan->jabatans[0]->mJabatan->level
                ]);
                $user->tokens()->delete();
            }

            return $this->okApiResponse('OK', 'Berhasil diupdate');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function detail($nip)
    {
        try {

            $karyawan = MKaryawanDetail::where('nip', $nip)->get();

            return $this->okApiResponse($karyawan, 'Berhasil dimuat');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function storeDetail(Request $request)
    {
        try {
            $str = $request->str;
            $sip = $request->sip;
            $nip = $request->nip;
            if ($str && !is_null($str['nomor'] !== '')) {
                $getStr = MKaryawanDetail::where([
                    'nip' => $nip,
                    'jenis' => 'str',
                ])->first();
                if (!is_null($getStr)) {
                    $getStr->update([
                        'nomor' => $str['nomor'],
                        'terbit' => $str['terbit'],
                        'akhir' => $str['akhir'],
                    ]);
                } else {
                    MKaryawanDetail::create([
                        'nip' => $nip,
                        'nomor' => $str['nomor'],
                        'terbit' => $str['terbit'],
                        'akhir' => $str['akhir'],
                        'jenis' => 'str',
                    ]);
                }
            }

            if ($sip && !is_null($sip['nomor'])) {
                $getSip = MKaryawanDetail::where([
                    'nip' => $nip,
                    'jenis' => 'sip',
                ])->first();
                if (!is_null($getSip)) {
                    $getSip->update([
                        'nomor' => $sip['nomor'],
                        'terbit' => $sip['terbit'],
                        'akhir' => $sip['akhir'],
                    ]);
                } else {
                    MKaryawanDetail::create([
                        'nip' => $nip,
                        'nomor' => $sip['nomor'],
                        'terbit' => $sip['terbit'],
                        'akhir' => $sip['akhir'],
                        'jenis' => 'sip',
                    ]);
                }
            }

            return $this->okApiResponse('OK', 'Berhasil dimuat');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function addDetail(Request $request)
    {
        try {
            $detail = MKaryawanDetail::create([
                'nip' => $request->nip,
                'jenis' => $request->jenis,
                'nomor' => $request->nomor,
                'terbit' => $request->terbit,
                'akhir' => $request->akhir,
            ]);

            return $this->okApiResponse($detail, 'Berhasil dimuat');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function destroyDetail($id)
    {
        try {
            $detail = MKaryawanDetail::findOrFail($id);
            $detail->delete();

            return $this->noContentApiResponse('Berhasil dimuat');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function removeDetail($id)
    {
        try {
            $detail = MKaryawanDetail::find($id);
            if (!is_null($detail)) {
                $detail->delete();
            }

            return $this->okApiResponse('OK', 'Berhasil dimuat');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function resign(Request $request, $nip)
    {
        try {
            $tglResign = Carbon::make($request->tgl_resign);
            $karyawan = MKaryawan::where('nip', $nip)->first();
            $karyawan->update([
                'tgl_resign' => $tglResign->format('Y-m-d')
            ]);

            return $this->okApiResponse('OK', 'Berhasil dimuat');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function byNip($nip)
    {
        try {

            $karyawan = DB::table('m_karyawan')->where('nip', $nip)
                ->select('id', 'nik', 'nip', 'nama', 'sex', 'status_user', 'id_unit', 'cuti')
                ->first();
            return $this->okApiResponse($karyawan, 'Berhasil ditambahkan');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function searchKaryawanUnit()
    {
        try {

            $q = request('q');
            $user = auth()->user();
            $karyawans = MKaryawan::with([
                'mUnit' => function ($query) {
                    $query->selectIdx();
                },
            ])
                ->select('nip', 'nama', 'id_unit')
                ->whereLike('nama', $q)
                ->when(in_array($user->role, [User::KEPALA]), function ($query) use ($user) {
                    $query->where('id_unit', $user->mKaryawan->id_unit);
                })
                ->orderByDesc('id')
                ->limit(5)
                ->get();

            return $this->okApiResponse(
                $karyawans,
                'Berhasil Memuat Data'
            );
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function getReqDevice()
    {
        try {
            $lists = ResetDevice::selectIdx()->whereNull('status')->get();
            return $this->okApiResponse(
                $lists,
                'Berhasil Memuat Data'
            );
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function updateDevice(Request $request, $id)
    {
        try {
            DB::beginTransaction();


            $list = ResetDevice::find($id);

            $user = User::where('nip', $request->nip)->first();
            $user->deviceweb_id = $list->to;
            $user->save();

            $list->status = ResetDevice::TERIMA;
            $list->save();

            DB::commit();

            return $this->okApiResponse(
                $list,
                'Berhasil Memuat Data'
            );
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function destroyReqDevice($id)
    {
        try {
            $list = ResetDevice::find($id);
            $list->delete();

            return $this->noContentApiResponse(
                'Berhasil'
            );
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function sipIndex()
    {
        try {

            $search = request('search');
            $unit = request('unit');
            $sortBy = request('sort_by');
            $sortColumn = request('sort_column');

            $list = MKaryawanDetail::with([
                'karyawan' => function ($query) {
                    $query->select('nip', 'nama', 'photo', 'id_unit');
                },
                'karyawan.mUnit' => function ($query) {
                    $query->select('id', 'nama');
                }
            ])
                ->select([
                    'id',
                    'nip',
                    'nomor',
                    'terbit',
                    'akhir',
                    'jenis',
                ])
                ->when($search !== null, function ($query) use ($search) {
                    $query->whereHas('karyawan', function ($query) use ($search) {
                        $query->where('nama', 'LIKE', "%{$search}%");
                    });
                })
                ->when($unit !== null, function ($query) use ($unit) {
                    $query->whereHas('karyawan', function ($query) use ($unit) {
                        $query->where('id_unit', "%{$unit}%");
                    });
                })
                ->when($sortColumn !== null && $sortBy !== null, function ($query) use ($sortBy, $sortColumn) {
                    $query->orderBy($sortColumn, $sortBy);
                }, function ($query) {
                    $query->latest();
                })
                ->get();

            return $this->okApiResponse(
                $list,
                'Berhasil'
            );
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function getNamaKaryawan()
    {
        try {
            $karyawan = DB::table('m_karyawan')->select(['id', 'nip', 'nama'])->get();

            return $this->okApiResponse(
                $karyawan,
                'Berhasil'
            );
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    /**
     * web
     */
    public function exportExcel()
    {
        try {

            $search = request('search');
            $unit = request('unit');
            $now = now()->format('d-m-Y');

            $karyawanExport = new MKaryawanExport([
                'unit' => $unit,
                'search' => $search,
            ]);

            return Excel::download($karyawanExport, "Karyawan export {$now}.xlsx");
        } catch (\Throwable $th) {

            abort(500, $th->getMessage());
        }
    }
}
