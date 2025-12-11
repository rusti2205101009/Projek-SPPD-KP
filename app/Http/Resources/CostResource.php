<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CostResource extends JsonResource
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
            'uang_perhari' => $this->uang_perhari,
            'lama_hari' => $this->lama_hari,
            'total_uang_harian' => $this->total_uang_harian,
            'uang_representasi' => $this->uang_representasi,
            'nama_hotel' => $this->nama_hotel,
            'biaya_akomodasi' => $this->biaya_akomodasi,
            'biaya_kontribusi' => $this->biaya_kontribusi,
            'biaya_tiket_berangkat' => $this->biaya_tiket_berangkat,
            'biaya_tiket_kembali' => $this->biaya_tiket_kembali,
            'biaya_bantuan_transport' => $this->biaya_bantuan_transport,
            'biaya_taxi_berangkat' => $this->biaya_taxi_berangkat,
            'biaya_taxi_kembali' => $this->biaya_taxi_kembali,
            'biaya_travel' => $this->biaya_travel,
            'biaya_tidak_menginap' => $this->biaya_tidak_menginap,
            'total_biaya' => $this->total_biaya,
            'bukti_file' => $this->bukti_file,
            'bukti_file_url' => $this->bukti_file 
                ? asset('storage/' . $this->bukti_file)
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
