<?php

namespace App\Models;

use App\Traits\FilterByUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Spt extends Model
{
    use HasFactory;
    // use FilterByUser;

    protected $table = 'spts';
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'head_division_id',
        'template_id',
        'nomor_surat',
        'tanggal_surat',
        'keperluan',
        'nip_kepala',
        'gelar_depan_kepala',
        'nama_kepala',
        'gelar_belakang_kepala',
        'jabatan_kepala',
        'pangkat_kepala',
        'golongan_kepala',
    ];

    // public function employees()
    // {
    //     return $this->belongsToMany(Employee::class, 'employee_spt', 'spt_id', 'employee_id')
    //                 ->withPivot(['gelar_depan', 'nama_pegawai', 'gelar_belakang', 'nip', 'jabatan', 'pangkat', 'golongan', 'bidang'])
    //                 ->withTimestamps();
    // }

    public function employeesSpt()
    {
        return $this->hasMany(EmployeeSpt::class, 'spt_id');
    }

    public function headDivision()
    {
        return $this->belongsTo(HeadDivision::class, 'head_division_id');
    }

    public function sppd()
    {
        return $this->hasOne(Sppd::class, 'spt_id');
    }

    public function costs()
    {
        return $this->hasMany(Cost::class, 'spt_id');
    }

    public function departures()
    {
        return $this->hasMany(Departure::class, 'spt_id');
    }

     public function returns()
    {
        return $this->hasMany(ReturnTrip::class, 'spt_id');
    }

    public function scopeByUser($query)
    {
        $user = Auth::user();
        if (!$user) return $query;

        if ($user->role === 'admin') {
            return $query;
        }

        $employeeId = $user->employee_id ?? null;

        return $query->where(function ($q) use ($user, $employeeId) {
            $q->where('user_id', $user->id)
              ->orWhereHas('employeesSpt', function ($sub) use ($employeeId) {
                  $sub->where('employee_id', $employeeId);
              });
        });
    }

    public function template()
    {
        return $this->belongsTo(Template::class);
    }
}
