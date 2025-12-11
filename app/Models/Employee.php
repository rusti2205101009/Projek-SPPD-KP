<?php

namespace App\Models;

use App\Traits\FilterByUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory, FilterByUser;
    
    protected $table = 'employees';
    protected $primaryKey = 'id';

    protected $fillable = [
        'foto',
        'nip_nipppk',
        'gelar_depan',
        'nama_pegawai',
        'gelar_belakang',
        'jabatan',
        'pangkat',
        'golongan',
    ];

    public function spts() 
    {
        return $this->belongsToMany(Spt::class, 'employee_spt', 'employee_id', 'spt_id')
                    ->withPivot(['gelar_depan', 'nama_pegawai', 'gelar_belakang', 'nip_nipppk', 'jabatan', 'pangkat', 'golongan', 'bidang'])
                    ->withTimestamps();
    }

    public function user()
    {
        return $this->hasOne(User::class, 'employee_id');
    }
}
