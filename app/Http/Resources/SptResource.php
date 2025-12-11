<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SptResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);

        return [
            'id' => $this->id,
            'head_division' => $this->whenLoaded('headDivision', function () {
                return [
                    'id' => $this->headDivision->id,
                    'nama_kepala' => trim(
                        ($this->headDivision->gelar_depan ? $this->headDivision->gelar_depan . ' ' : '') .
                        $this->headDivision->nama_kepala .
                        ($this->headDivision->gelar_belakang ? ', ' . $this->headDivision->gelar_belakang : '')
                    ),
                ];
            }),
            'template_id' => $this->template_id,
            'nomor_surat' => $this->nomor_surat,
            'tanggal_surat' => $this->tanggal_surat,
            'keperluan' => $this->keperluan,

            'employees' => $this->whenLoaded('employeesSpt', function () {
                return $this->employeesSpt->map(function ($pegawai) {
                    return [
                        // 'id' => $pegawai->id,
                        'employee_spt_id' => $pegawai->id,  // ID , optional
                        'employee_id' => $pegawai->employee_id,  // ID asli pegawai
                        'nama_pegawai'=> trim(
                            ($pegawai->gelar_depan ? $pegawai->gelar_depan . ' ' : '') .
                            $pegawai->nama_pegawai .
                            ($pegawai->gelar_belakang ? ', ' . $pegawai->gelar_belakang : '')
                        ),
                        'nip_nipppk'      => $pegawai->nip_nipppk,
                        'jabatan'  => $pegawai->jabatan,
                        'pangkat'  => $pegawai->pangkat,
                        'golongan' => $pegawai->golongan,
                        'bidang'   => $pegawai->bidang,
                    ];
                });
            }),

            'nip_kepala' => $this->nip_kepala,
            'gelar_depan_kepala' => $this->gelar_depan_kepala ?? '',
            'nama_kepala' => $this->nama_kepala,
            'gelar_belakang_kepala' => $this->gelar_belakang_kepala ?? '',
            'jabatan_kepala' => $this->jabatan_kepala,
            'pangkat_kepala' => $this->pangkat_kepala,
            'golongan_kepala' => $this->golongan_kepala,

        'sppd' => new SppdResource($this->whenLoaded('sppd')),
        ];
    }
}
