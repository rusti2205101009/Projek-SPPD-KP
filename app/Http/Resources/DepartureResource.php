<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepartureResource extends JsonResource
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
            'spt_id' => $this->spt_id,
            'sppd_id' => $this->sppd_id,
            'employee_id' => $this->employee_id,
            'no_bukti' => $this->no_bukti,
            'tanggal_bukti' => $this->tanggal_bukti,
            'no_tiket_berangkat' => $this->no_tiket_berangkat,
            'nama_transportasi' => $this->nama_transportasi,
            'tempat_asal' => $this->tempat_asal,
            'daerah_tujuan' => $this->daerah_tujuan,
            'tempat_tujuan' => $this->tempat_tujuan,
            'tanggal_berangkat_tiket' => $this->tanggal_berangkat_tiket,
            'bukti_file_berangkat' => $this->bukti_file_berangkat,
            'bukti_file_berangkat_url' => $this->bukti_file_berangkat 
                ? asset('storage/' . $this->bukti_file_berangkat)
                : null,

            'nomor_surat' => $this->whenLoaded('spt', fn() => $this->spt->nomor_surat),
            
           'sppd' => $this->whenLoaded('sppd', fn() => new SppdResource($this->sppd)),

            'pegawai' => $this->whenLoaded('employeeSpt', function () {
                $pivot = $this->employeeSpt;

                $namaLengkap = trim(
                    ($pivot->gelar_depan ? $pivot->gelar_depan . ' ' : '') .
                    $pivot->nama_pegawai .
                    ($pivot->gelar_belakang ? ', ' . $pivot->gelar_belakang : '')
                );

                return [
                    'id' => $pivot->id,
                    'nama_pegawai' => $namaLengkap,
                ];
            }),
        ];
    }
}
