<?php

namespace App\Http\Requests;

use App\Models\Izin;
use App\Models\MIzin;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\RequiredIf;

class StoreIzinRequest extends FormRequest
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
            'izin' => ['required'],
            'mulai' => ['required'],
            'akhir' => ['required'],
            'masuk' => ['required'],
            'periode' => ['required'],
            'ket' => []
        ];
    }
}
