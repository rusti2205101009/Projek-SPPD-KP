<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HeadDivision extends Model
{
    use HasFactory;

    protected $table = 'head_divisions';
    protected $primaryKey = 'id';

    protected $fillable = [
        'foto',
        'nip',
        'gelar_depan',
        'nama_kepala',
        'gelar_belakang',
        'jabatan',
        'pangkat',
        'golongan',
        'ttd',
    ];
}
