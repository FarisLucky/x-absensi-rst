<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMShiftRequest extends FormRequest
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
            'kode' => ['required', "unique:m_shift,kode"],
            'nama' => ['required'],
            'mulai_absen' => ['required'],
            'jam_masuk' => ['required'],
            'telat_masuk' => ['required'],
            'jam_pulang' => ['required'],
            'telat_pulang' => ['required'],
        ];
    }
}
