<?php

namespace App\Http\Controllers;

use App\Models\Sppd;
use App\Models\Departure;
use App\Models\ReturnTrip;
use Illuminate\Http\Request;
use App\Http\Resources\DepartureResource;
use App\Http\Resources\DepartureCollection;

class ApiDepartureController extends Controller
{
    public function indexDeparture(Request $request)
    {
        $search = $request->get('search');

        $departures = Departure::byUser()->with(['employeeSpt', 'spt', 'sppd']);

        if ($search) {
            $departures->where(function ($query) use ($search) {
            $query->where('no_bukti', 'like', "%$search%")
                ->orWhere('tanggal_bukti', 'like', "%$search%")
                ->orWhere('no_tiket_berangkat', 'like', "%$search%")
                ->orWhere('daerah_tujuan', 'like', "%$search%")
                ->orWhere('_tempat_tujuan', 'like', "%$search%")
                ->orWhere('tanggal_berangkat_tiket', 'like', "%$search%")

                ->orWhereHas('spt', function ($q) use ($search) {
                    $q->where('nomor_surat', 'like', "%$search%");
                })

                ->orWhereHas('employeesSpt', function ($q) use ($search) {
                    $q->where('nama_pegawai', 'like', "%$search%")
                      ->orWhere('gelar_depan', 'like', "%$search%")
                      ->orWhere('gelar_belakang', 'like', "%$search%");
                });
            });
        }

        $paginated = $departures->orderBy('created_at', 'desc')->paginate(10);
        
        return new DepartureCollection($paginated);
    }

    public function showDeparture($id)
    {
        $departure = Departure::byUser()->with(['employeeSpt', 'spt', 'sppd'])->findOrFail($id);
        return new DepartureResource($departure);
    }

    public function updateDeparture(Request $request, $id)
    {
        $departure = Departure::byUser()->findOrFail($id);

        $validated = $request->validate([
            'spt_id' => 'nullable|exists:spts,id',
            'sppd_id' => 'nullable|exists:sppds,id',
            'employee_spt_id' => 'nullable|exists:employee_spt,id',
            'no_bukti' => 'nullable|string|max:30',
            'tanggal_bukti' => 'nullable|date',
            'no_tiket_berangkat' => 'nullable|string|max:20',
            'nama_transportasi' => 'nullable|string|max:30',
            'tempat_asal' => 'nullable|string|max:30',
            // 'tanggal_berangkat_tiket' => 'nullable|date',
            'bukti_file_berangkat' => 'nullable|file|mimes:pdf|max:10048'
        ]);
        
        $updateData = $validated;

        if ($request->hasFile('bukti_file_berangkat')) {
            if ($departure->bukti_file_berangkat && \Storage::exists($departure->bukti_file_berangkat)) {
                \Storage::delete($departure->bukti_file_berangkat);
            }

            $path = $request->file('bukti_file_berangkat')->store('bukti_files_berangkat', 'public');
            $updateData['bukti_file_berangkat'] = $path;
        }

        if (!empty($validated['sppd_id'])) {
            $sppd = Sppd::find($validated['sppd_id']);
            if ($sppd) {
                $updateData['daerah_tujuan'] = $sppd->daerah;
                $updateData['tempat_tujuan'] = $sppd->tujuan;
                $updateData['tanggal_berangkat_tiket'] = $sppd->tanggal_berangkat;
            }
        }

        $departure->fill($updateData);
        $departure->save();

        $returnTrip = ReturnTrip::firstOrNew(['departure_id' => $departure->id]);
        $returnTrip->sppd_id    = $departure->sppd_id;
        $returnTrip->employee_spt_id = $departure->employee_spt_id;
        $returnTrip->spt_id     = $departure->spt_id;

        $returnTrip->daerah_asal   = $departure->daerah_tujuan;   
        $returnTrip->tempat_asal   = $departure->tempat_tujuan;
        $returnTrip->tempat_tujuan = $departure->tempat_asal;
        $returnTrip->save();

        return response()->json([
            'message' => 'Data Keberangkatan Berhasil Diperbarui',
            'data' => new DepartureResource($departure->load(['employeeSpt', 'spt', 'sppd'])),
        ]);
    }
}
