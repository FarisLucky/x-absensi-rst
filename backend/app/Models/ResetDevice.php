<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResetDevice extends Model
{
    const TERIMA = 1;
    const TOLAK = 2;

    protected $table = "reset_device";

    protected $fillable = [
        'nip',
        'nama',
        'from',
        'platform',
        'to',
        'ket'
    ];

    public $casts = [
        "created_at" => "datetime",
        "updated_at" => "datetime",
    ];

    public function scopeSelectIdx($query)
    {
        return $query->select(
            'id',
            'nip',
            'nama',
            'from',
            'to',
            'ket'
        );
    }
}
