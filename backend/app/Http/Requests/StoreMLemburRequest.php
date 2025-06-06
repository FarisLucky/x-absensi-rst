<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMLemburRequest extends FormRequest
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
            'nama' => ['required'],
            'harga' => ['required'],
            'ttl_jam' => [''],
            'id_lokasi' => [''],
            'absen_foto' => [''],
            'status' => [''],
            'anggota' => [''],
        ];
    }
}
