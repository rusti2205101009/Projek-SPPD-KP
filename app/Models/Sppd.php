<?php

namespace App\Models;

use App\Traits\FilterByUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sppd extends Model
{
    use HasFactory;
    // use FilterByUser;

    protected $table = 'sppds';
    protected $primaryKey = 'id';

    protected $fillable = [
        'spt_id',
        'daerah',
        'tujuan',
        'moda_transport',
        'tanggal_berangkat',
        'tanggal_kembali',
        'lama_hari',
    ];

    public function spt()
    {
        return $this->belongsTo(Spt::class, 'spt_id');
    }

    public function costs()
    {
        return $this->hasMany(Cost::class, 'sppd_id');
    }

    public function departures()
    {
        return $this->hasMany(Departure::class, 'sppd_id');
    }

    public function returnTrips()
    {
        return $this->hasMany(ReturnTrip::class, 'sppd_id');
    }

    public function scopeByUser($query)
    {
        return $query->whereHas('spt', function ($q) {
            $q->getModel()->scopeByUser($q);
        });
    }
}
