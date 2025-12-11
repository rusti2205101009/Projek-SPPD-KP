<?php

namespace App\Models;

use App\Traits\FilterByUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Departure extends Model
{
    use HasFactory;
    // use FilterByUser;

    protected $table = 'departures';
    protected $primaryKey = 'id';

    protected $fillable = [
        'spt_id',
        'sppd_id',
        'employee_spt_id',
        'no_bukti',
        'tanggal_bukti',
        'no_tiket_berangkat',
        'nama_transportasi',
        'tempat_asal',
        'daerah_tujuan',
        'tempat_tujuan',
        'tanggal_berangkat_tiket',
        'bukti_file_berangkat',
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

    public function scopeByUser($query)
    {
        return $query->whereHas('spt', function ($q) {
            $q->getModel()->scopeByUser($q);
        });
    }
}
