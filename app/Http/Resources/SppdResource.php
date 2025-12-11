<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SppdResource extends JsonResource
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
            'daerah' => $this->daerah,
            'tujuan' => $this->tujuan,
            'moda_transport' => $this->moda_transport
                ? array_map('trim', explode(',', $this->moda_transport))
                : [],
            'tanggal_berangkat' => $this->tanggal_berangkat,
            'tanggal_kembali' => $this->tanggal_kembali,
            'lama_hari' => $this->lama_hari,

            'spt' => $this->whenLoaded('spt', function () {
                return new SptResource($this->spt);
            }),
        ];
    }
}
