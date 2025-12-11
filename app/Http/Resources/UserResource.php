<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'nip_nipppk' => $this->nip_nipppk,
            'role' => $this->role,
            'employee_id' => $this->employee_id,
            'foto' => $this->foto ? asset('storage/' . $this->foto) : null,
            'pegawai' => $this->whenLoaded('employee', function () {
            return [
                'id' => $this->employee->id,
                'nama_pegawai' => $this->employee->nama_pegawai,
                ];
            }),
        ];
    }
}
