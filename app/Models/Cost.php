<?php

namespace App\Models;

use App\Models\EmployeeSpt;
use App\Traits\FilterByUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cost extends Model
{
    use HasFactory;
    // use FilterByUser;

    protected $table = 'costs';
    protected $primaryKey = 'id';

    protected $fillable = [
        'spt_id',
        'sppd_id',
        'employee_spt_id',
        'uang_perhari',
        'lama_hari',
        'total_uang_harian',
        'uang_representasi',
        'nama_hotel',
        'biaya_akomodasi',
        'biaya_kontribusi',
        'biaya_tiket_berangkat',
        'biaya_tiket_kembali',
        'biaya_bantuan_transport',
        'biaya_taxi_berangkat',
        'biaya_taxi_kembali',
        'biaya_travel',
        'biaya_tidak_menginap',
        'total_biaya',
        'bukti_file',
    ];

    public function employeeSpt() 
    {
        return $this->belongsTo(EmployeeSpt::class, 'employee_spt_id');
    }

    public function spt()
    {
        return $this->belongsTo(Spt::class, 'spt_id');
    }

    public function sppd()
    {
        return $this->belongsTo(Sppd::class, 'sppd_id');
    }

    protected static function booted()
    {
        static::saving(function ($cost) {
            $lamaHari = $cost->sppd ? $cost->sppd->lama_hari : 0;

            $cost->total_uang_harian = $lamaHari * ($cost->uang_perhari ?? 0);

            $cost->total_biaya = 
                ($cost->total_uang_harian ?? 0) +
                ($cost->uang_representasi ?? 0) +
                ($cost->biaya_akomodasi ?? 0) +
                ($cost->biaya_kontribusi ?? 0) +
                ($cost->biaya_tiket_berangkat ?? 0) +
                ($cost->biaya_tiket_kembali ?? 0) +
                ($cost->biaya_bantuan_transport ?? 0) +
                ($cost->biaya_taxi_berangkat ?? 0) +
                ($cost->biaya_taxi_kembali ?? 0) +
                ($cost->biaya_travel ?? 0) +
                ($cost->biaya_tidak_menginap ?? 0);
        });
    }

    public function scopeByUser($query)
    {
        return $query->whereHas('spt', function ($q) {
           $q->getModel()->scopeByUser($q);
        });
    }
}
