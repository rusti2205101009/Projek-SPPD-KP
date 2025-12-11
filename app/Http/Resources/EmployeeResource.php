<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
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
            'nip_nipppk' => $this->nip_nipppk,
            'gelar_depan' => $this->gelar_depan ?? '',
            'nama_pegawai' => $this->nama_pegawai,
            'gelar_belakang' => $this->gelar_belakang ?? '',
            'jabatan' => $this->jabatan,
            'pangkat' => $this->pangkat,
            'golongan' => $this->golongan,
        ];
    }
}
