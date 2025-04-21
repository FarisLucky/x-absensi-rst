<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMKaryawanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'nik' => ['required', Rule::unique('m_karyawan')->where(function ($query) {
                $query->where('nip', auth()->user()->nip);
            })],
            'nip' => ['required', Rule::unique('m_karyawan')->where(function ($query) {
                $query->where('nip', auth()->user()->nip);
            })],
            'nama' => ['required'],
            'sex' => ['required'],
            'tgl_lahir' => ['required'],
            'tempat_lahir' => ['required'],
            'pendidikan' => ['required'],
            'prov' => [''],
            'kab' => [''],
            'kec' => [''],
            'desa' => [''],
            'kodepos' => [''],
            'alamat' => [''],
            'agama' => [''],
            'telp' => [''],
            'email' => [''],
            'id_unit' => ['required'],
            'jabatan' => ['required'],
            'jml_cuti' => [''],
            'tgl_masuk' => [''],
            'status_kerja' => [''],
            'tgl_resign' => [''],
        ];
    }
}
