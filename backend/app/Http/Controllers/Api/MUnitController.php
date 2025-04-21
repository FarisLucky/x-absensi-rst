<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ApiResponse;
use App\Http\Requests\StoreMUnitRequest;
use App\Http\Requests\UpdateMUnitRequest;
use App\Models\MKaryawan;
use App\Models\MUnit;
use Illuminate\Http\Request;

class MUnitController extends Controller
{
    use ApiResponse;

    public function index()
    {
        try {

            $units = MUnit::selectIdx()->orderBydesc('id')->get();

            return $this->okApiResponse(
                $units,
                'Berhasil Memuat Data'
            );
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function data()
    {
        try {

            $units = MUnit::selectIdx()->get();

            return $this->okApiResponse(
                $units,
                'Berhasil Memuat Data'
            );
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function store(StoreMUnitRequest $request)
    {

        try {
            $input = $request->validated();

            $unit = MUnit::create($input);

            return $this->okApiResponse($unit, 'Berhasil disimpan');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function show($id)
    {
        try {

            $unit = MUnit::find($id);

            return $this->okApiResponse($unit, 'Berhasil dimuat');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function update(UpdateMUnitRequest $request, $id)
    {
        try {

            $input = $request->validated();
            $unit = MUnit::find($id);

            $unit->update($input);

            return $this->okApiResponse($unit, 'Berhasil diperbarui');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function destroy($id)
    {
        try {

            $unit = MUnit::find($id);
            $unit->delete();

            return $this->noContentApiResponse('');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function storeOrUpdate(Request $request)
    {

        try {

            $payload = [
                'id_unit' => $request->id_unit,
            ];

            MKaryawan::where('nip', $request->nip)->update($payload);

            return $this->okApiResponse('OK', 'Berhasil disimpan');
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function getBawahan()
    {
        $bawahan = MJabatan::when($bawahanFilter === 'semua', function ($query) use ($user) {
            $query->with('children')->where('id_parent', $user->jabatans[0]->id_jabatan);
        }, function ($query) use ($user) {
            $query->where('id_parent', $user->jabatans[0]->id_jabatan);
        })->get();

        $query->whereHas('jabatans', function ($query) use ($bawahan) {
            $listId = [];
            /**
             * FILTER DATA SEMUA BAWAHAN
             */
            $childFunc = function ($c, &$listId) use (&$childFunc) {
                if (is_null($c)) {
                    return;
                }
                foreach ($c as $ch) {
                    array_push($listId, $ch->id);
                    if (!is_null($ch->children)) {
                        $childFunc($ch->children, $listId);
                    }
                }
            };
            $childFunc($bawahan, $listId);
            $query->whereIn('id_jabatan', $listId);
        });
    }
}
