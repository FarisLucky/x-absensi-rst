<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ApiResponse;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    use ApiResponse;

    public function login(LoginRequest $request)
    {
        $user = User::with(['jabatans', 'jabatans.mJabatan'])
            ->where('nip', $request->nip)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'nip' => ['Akun tidak ditemukan']
            ]);
        }
        // $existingSession = DB::table('sessions')
        //     ->where('user_id', $user->id)
        //     ->exists();
        // if ($existingSession) {
        //     throw ValidationException::withMessages([
        //         'session' => ['Anda sudah login di perangkat lain. Silakan logout terlebih dahulu.']
        //     ]);
        // }

        // Auth::login($user);

        // DB::table('sessions')->where('user_id', $user->id)->delete();

        // $request->session()->regenerate();

        $token = $user->createToken($request->device ?? 'android')->plainTextToken;

        return $this->okApiResponse([
            'user' => $user,
            'token' => $token
        ]);
    }

    public function logout()
    {
        try {
            $user = auth()->user();
            Log::info('user', [$user->currentAccessToken()]);

            $user->currentAccessToken()->delete();
            Auth::logout();

            return $this->okApiResponse([], 'Berhasil Logout');
        } catch (\Throwable $th) {
            return $this->errorApiResponse($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function changePassword(Request $request)
    {
        try {
            $user = auth()->user();
            $password = $request->password;

            User::where('id', $user->id)->update([
                'password' => Hash::make($password)
            ]);

            return response()->json($password, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
