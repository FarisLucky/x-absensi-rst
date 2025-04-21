<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ApiResponse;
use App\Models\Companies;
use Illuminate\Http\Request;

class CompaniesController extends Controller
{
    use ApiResponse;

    public function index()
    {
        try {
            $company = Companies::first();

            return $this->okApiResponse(
                $company,
                'Berhasil Memuat Data'
            );
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $company = Companies::find($id);

            $validated = $request->validate([
                'nama' => 'required|string|max:255',
                'alamat' => 'required|string',
                'telp' => 'required|string',
                'email' => 'required|email',
            ]);

            if ($request->hasFile('logo')) {
                $path = $request->file('logo')->storeAs('logopt', 'logo.png');
                $validated['logo'] = $path;
            }
            if (is_null($company)) {
                Companies::create($validated);
            } else {
                $company->update($validated);
            }


            return $this->okApiResponse(
                $company,
                'Berhasil Memuat Data'
            );
        } catch (\Throwable $th) {

            return $this->errorApiResponse($th->getMessage());
        }
    }
}
