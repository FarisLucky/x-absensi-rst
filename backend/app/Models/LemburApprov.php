<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class LemburApprov extends Model
{
    protected $table = "lembur_approv";

    protected $fillable = [
        'id',
        'id_lembur',
        'acc_by',
        'acc_nama',
        'acc_at',
        'status',
        'ket',
    ];
    protected $appends = ['acc_at_cast'];

    public $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function scopeSelectIdx($query)
    {
        return $query->select(
            'id',
            'id_lembur',
            'acc_by',
            'acc_nama',
            'acc_at',
            'status',
            'ket',
        );
    }
    // JOIN CASTING
    public function getAccAtCastAttribute()
    {
        return !is_null($this->acc_at) ? Carbon::make($this->acc_at)->isoFormat('ll HH:mm') : $this->acc_at;
    }
}
