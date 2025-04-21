<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMKaryawanRequest extends FormRequest
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
            'nik' => ['required', 'unique:m_karyawan,nik'],
            'nip' => ['required', 'unique:m_karyawan,nip'],
            'nama' => ['required'],
            'sex' => ['required'],
            'tgl_lahir' => ['required'],
            'tempat_lahir' => [''],
            'pendidikan' => [''],
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
