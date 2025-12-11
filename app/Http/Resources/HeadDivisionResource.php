<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HeadDivisionResource extends JsonResource
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
            'foto' => $this->foto
                ? asset('storage/' . $this->foto)
                : asset('images/user-avatar.png'),
            'nip' => $this->nip,
            'gelar_depan' => $this->gelar_depan ?? '',
            'nama_kepala' => $this->nama_kepala,
            'gelar_belakang' => $this->gelar_belakang ?? '',
            'jabatan' => $this->jabatan,
            'pangkat' => $this->pangkat,
            'golongan' => $this->golongan,
            'ttd' => $this->ttd
                ? asset('storage/' . $this->ttd)
                : null,
        ];
    }
}
