<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ApiResponse;
use App\Http\Requests\LoginRequest;
use App\Models\ResetDevice;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginOneDeviceController extends Controller
{
    use ApiResponse;

    public function login(LoginRequest $request)
    {

        $deviceId = $request->header('X-DEVICE-ID');
        // $devicePlatform = $request->header('X-DEVICE-PLATFORM');
        $deviceOs = $request->header('X-DEVICE-OS');

        $user = User::join('m_karyawan', 'm_karyawan.nip', '=', 'users.nip')
            ->where('users.nip', $request->nip)
            ->first([
                'users.id',
                'users.nama',
                'users.nip',
                'users.password',
                'users.role',
                'm_karyawan.jabatan',
                'm_karyawan.unit',
                'm_karyawan.id_unit',
                'm_karyawan.tgl_resign',
            ]);

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'nip' => ['Akun tidak ditemukan']
            ]);
        } else if ($user->tgl_resign !== null && $user->tgl_resign !== '') {
            throw ValidationException::withMessages([
                'nip' => ['Akun sudah tidak aktif']
            ]);
        } elseif ($user->role === User::SUPER_ADMIN && $deviceOs === 'android') {
            // do nothing
        } else if ($deviceOs === 'android') {
            $perangkat = User::where('device_hash', $deviceId)
                ->first(['device_hash', 'nip', 'nama']);

            if (is_null($perangkat)) {
                $user->device_hash = $deviceId;
                $user->save();
            } else if ($perangkat->nip !== $request->nip) {
                throw ValidationException::withMessages([
                    'session' => ['Bukan Perangkat Anda !']
                ]);
            }
        }
        $user->last_login_at = now();
        $user->save();

        $token = $user->createToken($request->deviceOs ?? 'android')->plainTextToken;

        return $this->okApiResponse([
            'user' => $user->only([
                'nama',
                'nip',
                'password',
                'role',
                'jabatan',
                'unit',
                'id_unit',
                'tgl_resign',
            ]),
            'token' => $token
        ]);
    }

    public function logout()
    {
        try {
            $user = auth()->user();
            $user->currentAccessToken()->delete();

            return $this->okApiResponse([], 'Berhasil Logout');
        } catch (\Throwable $th) {
            return $this->errorApiResponse($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function changePassword(Request $request)
    {
        try {
            $user = auth()->user();
            $user->password = Hash::make($request->password);
            $user->save();

            return response()->json($password, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function pengajuanDevice(Request $request)
    {
        try {

            $user = User::where('nip', $request->nip)->first();
            if (is_null($user)) {
                throw new Exception('NIP tidak ditemukan');
            }

            $payload = [
                'nip' => $user->nip,
                'nama' => $user->nama,
                'from' => $user->deviceweb_id,
                'to' => $request->deviceId,
                'platform' => $request->deviceOs,
                'ket' => $request->ket,
            ];

            ResetDevice::create($payload);

            return response()->json('OK', Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
