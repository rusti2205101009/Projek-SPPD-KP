<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReturnTripResource extends JsonResource
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
            'departure_id' => $this->departure_id,
            'employee_id' => $this->employee_id,
            'tanggal_kembali_tiket' => $this->tanggal_kembali_tiket,
            'no_bukti' => $this->no_bukti,
            'tanggal_bukti' => $this->tanggal_bukti,
            'no_tiket_kembali' => $this->no_tiket_kembali,
            'nama_transportasi' => $this->nama_transportasi,
            'daerah_asal' => $this->daerah_asal,
            'tempat_asal' => $this->tempat_asal,
            'tempat_tujuan' => $this->tempat_tujuan,
            'bukti_file_pulang' => $this->bukti_file_pulang,
            'bukti_file_pulang_url' => $this->bukti_file_pulang 
                ? asset('storage/' . $this->bukti_file_pulang)
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
