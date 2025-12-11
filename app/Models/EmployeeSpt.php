<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeSpt extends Model
{
    use HasFactory;

    protected $table = 'employee_spt';

    protected $fillable = [
        'employee_id',
        'spt_id',
        'gelar_depan',
        'nama_pegawai',
        'gelar_belakang',
        'nip_nipppk',
        'jabatan',
        'pangkat',
        'golongan',
        'bidang',
    ];

    public function spt()
    {
        return $this->belongsTo(Spt::class, 'spt_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function costs()
    {
        return $this->hasOne(Cost::class, 'employee_spt_id');
    }

    public function departures()
    {
        return $this->hasOne(Departure::class, 'employee_spt_id');
    }

    public function returnTrips()
    {
        return $this->hasOne(ReturnTrip::class, 'employee_spt_id');
    }
}
