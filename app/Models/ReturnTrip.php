<?php

namespace App\Models;

use App\Models\EmployeeSpt;
use App\Traits\FilterByUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReturnTrip extends Model
{
    use HasFactory;
    // use FilterByUser;

    protected $table = 'returns';
    protected $primaryKey = 'id';

    protected $fillable = [
        'spt_id',
        'departure_id',
        'employee_spt_id',
        'sppd_id',
        'tanggal_kembali_tiket',
        'no_bukti',
        'tanggal_bukti',
        'no_tiket_kembali',
        'nama_transportasi',
        'daerah_asal',
        'tempat_asal',
        'tempat_tujuan',
        'bukti_file_pulang',
    ];

    public function spt()
    {
        return $this->belongsTo(Spt::class, 'spt_id');
    }

    public function departure() 
    {
        return $this->belongsTo(Departure::class, 'departure_id');
    }

    public function sppd()
    {
        return $this->belongsTo(Sppd::class, 'sppd_id');
    }

    public function employeeSpt()
    {
        return $this->belongsTo(EmployeeSpt::class, 'employee_spt_id');
    }

    public function scopeByUser($query)
    {
        return $query->whereHas('spt', function ($q) {
           $q->getModel()->scopeByUser($q);
        });
    }
}
