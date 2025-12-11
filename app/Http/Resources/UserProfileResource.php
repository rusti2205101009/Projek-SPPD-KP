<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);

        $employee = $this->user->employee ?? null;

        $namaLengkap = $employee
            ? trim(($employee->gelar_depan ? $employee->gelar_depan . ' ' : '') .
                   $employee->nama_pegawai .
                   ($employee->gelar_belakang ? '., ' . $employee->gelar_belakang : ''))
            : null;

        return [
            'id'            => $this->id,
            'user_id'       => $this->user_id,

            'nama'          => $namaLengkap,
            'nip_nipppk'           => $employee->nip_nipppk ?? null,
            'jabatan'       => $employee->jabatan ?? null,

            'jenis_kelamin' => $this->jenis_kelamin,
            'email'         => $this->email,
            'nohp'          => $this->nohp,
            'foto'          => $this->foto ? asset('storage/' . $this->foto) : null,

            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'role' => $this->user->role,
                ];
            }),
        ];
    }
}
