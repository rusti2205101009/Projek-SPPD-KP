<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Cost;
use App\Models\Sppd;
use App\Models\Departure;
use App\Models\ReturnTrip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\SppdResource;
use App\Http\Resources\SppdCollection;

class ApiSppdController extends Controller
{
    public function indexSppd(Request $request)
    {
        $search = $request->get('search');

        $sppds = Sppd::byUser()->with(['spt.employeesSpt']);

        if ($search) {
            $sppds->where(function ($query) use ($search) {
            $query->where('daerah', 'like', "%$search%")
                ->orWhere('tujuan', 'like', "%$search%")
                ->orWhere('tanggal_berangkat', 'like', "%$search%")
                ->orWhere('tanggal_kembali', 'like', "%$search%")

                ->orWhereHas('spt', function ($q) use ($search) {
                    $q->where('nomor_surat', 'like', "%$search%");
                })

                ->orWhereHas('spt.employeesSpt', function ($q) use ($search) {
                    $q->where('nama_pegawai', 'like', "%$search%")
                      ->orWhere('gelar_depan', 'like', "%$search%")
                      ->orWhere('gelar_belakang', 'like', "%$search%")
                      ->orWhere('nip_nipppk', 'like', "%$search%")
                      ->orWhere('jabatan', 'like', "%$search%")
                      ->orWhere('golongan', 'like', "%$search%")
                      ->orWhere('pangkat', 'like', "%$search%");
                });
            });
        }

        $paginated = $sppds->orderBy('created_at', 'desc')->paginate(10);
        return new SppdCollection($paginated);
    }

    public function showSppd($id)
    {
        $sppd = Sppd::byUser()->with(['spt.employeesSpt'])->findOrFail($id);
        return new SppdResource($sppd);
    }

    public function updateSppd(Request $request, $id)
    {
        $sppd = Sppd::byUser()->findOrFail($id);

        $validated = $request->validate([
            'spt_id' => 'nullable|exists:spts,id',
            'daerah' => 'nullable|string|max:50',
            'tujuan' => 'nullable|string|max:50',
            'moda_transport' => 'nullable|array',
            'moda_transport.*' => 'in:Darat,Udara,Air',
            'tanggal_berangkat' => 'nullable|date',
            'tanggal_kembali' => 'nullable|date',
            'lama_hari' => 'nullable|integer',
        ]);

        $lama_hari = $sppd->lama_hari ?? 0;

        
        if (!empty($validated['tanggal_berangkat']) && !empty($validated['tanggal_kembali'])) {
            $lama_hari = Carbon::parse($validated['tanggal_berangkat'])
            ->diffInDays(Carbon::parse($validated['tanggal_kembali'])) + 1;
            $validated['lama_hari'] = $lama_hari;
        }   

        // array jadi string
        if (isset($validated['moda_transport']) && is_array($validated['moda_transport'])) {
            $validated['moda_transport'] = implode(', ', $validated['moda_transport']);
        }
        
        DB::beginTransaction();

        try {
            $sppd->update($validated);

            // $employees      = $sppd->spt ? $sppd->spt->employees : collect();
            // $pegawaiList = $employees->pluck('id')->toArray();

            $employeeSpts = $sppd->spt ? $sppd->spt->employeesSpt : collect(); 
            $employeeSptIds = $employeeSpts->pluck('id')->toArray();

            // Departure::where('sppd_id', $sppd->id)->whereNotIn('employee_id', $pegawaiList)->delete();
            // ReturnTrip::where('sppd_id', $sppd->id)->whereNotIn('employee_id', $pegawaiList)->delete();

            Departure::where('sppd_id', $sppd->id)
                ->whereNotIn('employee_spt_id', $employeeSptIds)
                ->delete();

            ReturnTrip::where('sppd_id', $sppd->id)
                ->whereNotIn('employee_spt_id', $employeeSptIds)
                ->delete();


            foreach ($employeeSpts as $empSpt) {
                Departure::updateOrCreate(
                    [
                        'sppd_id'   => $sppd->id,
                        'employee_spt_id' => $empSpt->id,
                    ],
                    [
                        'spt_id'        => $sppd->spt_id,
                        'daerah_tujuan' => $validated['daerah'],
                        'tempat_tujuan' => $validated['tujuan'],
                        'tanggal_berangkat_tiket' => $validated['tanggal_berangkat'],
                    ]
                );

                ReturnTrip::updateOrCreate(
                    [
                        'sppd_id'   => $sppd->id,
                        'employee_spt_id' => $empSpt->id,
                    ],
                    [
                        'spt_id'      => $sppd->spt_id,
                        'daerah_asal' => $validated['daerah'],
                        'tempat_asal' => $validated['tujuan'],
                        'tanggal_kembali_tiket' => $validated['tanggal_kembali'],
                    ]
                );
            }

            foreach ($sppd->costs as $cost) {
                $cost->lama_hari = $lama_hari;
                $cost->save();
            }

            DB::commit();

            return response()->json([
                'message' => 'Surat Perintah Perjalanan Dinas berhasil diperbarui',
                'data' => new SppdResource($sppd->load('spt.employeesSpt'))
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Gagal update Surat Perintah Perjalanan Dinas',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}