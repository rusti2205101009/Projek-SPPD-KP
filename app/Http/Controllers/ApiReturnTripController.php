<?php

namespace App\Http\Controllers;

use App\Models\Sppd;
use App\Models\Departure;
use App\Models\ReturnTrip;
use Illuminate\Http\Request;
use App\Http\Resources\ReturnTripResource;
use App\Http\Resources\ReturnTripCollection;

class ApiReturnTripController extends Controller
{
    public function indexReturnTrip(Request $request)
    {
        $search = $request->get('search');

        $return_trips = ReturnTrip::byUser()->with(['employeeSpt', 'sppd', 'spt', 'departure']);

        if ($search) {
            $return_trips->where(function ($query) use ($search) {
            $query->where('no_bukti', 'like', "%$search%")
                ->orWhere('tanggal_bukti', 'like', "%$search%")
                ->orWhere('no_tiket_kembali', 'like', "%$search%")
                ->orWhere('daerah_asal', 'like', "%$search%")
                ->orWhere('tempat_asal', 'like', "%$search%")
                ->orWhere('tanggal_kembali_tiket', 'like', "%$search%")

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

        $return_trip = $return_trips->orderBy('created_at', 'desc')->paginate(10);

        return new ReturnTripCollection($return_trip);
    }

    public function showReturnTrip($id)
    {
        $return_trip = ReturnTrip::byUser()->with(['employeeSpt', 'sppd', 'spt', 'departure'])->findOrFail($id);
        return new ReturnTripResource($return_trip);
    }

     public function updateReturnTrip(Request $request, $id)
    {
        $return_trip = ReturnTrip::byUser()->findOrFail($id);

        $validated = $request->validate([
            'spt_id' => 'nullable|exists:spts,id',
            'sppd_id' => 'nullable|exists:sppds,id',
            'employee_spt_id' => 'nullable|exists:employee_spt,id',
            'departure_id' => 'nullable|exists:departures,id',
            // 'tanggal_kembali_tiket' => 'nullable|date',
            'no_bukti' => 'nullable|string|max:30',
            'tanggal_bukti' => 'nullable|date',
            'no_tiket_kembali' => 'nullable|string|max:20',
            'nama_transportasi' => 'nullable|string|max:30',
            'bukti_file_pulang' => 'nullable|file|mimes:pdf|max:10048'
        ]);

        if ($request->hasFile('bukti_file_pulang')) {
            if ($return_trip->bukti_file_pulang && \Storage::exists($return_trip->bukti_file_pulang)) {
                \Storage::delete($return_trip->bukti_file_pulang);
            }
            $path = $request->file('bukti_file_pulang')->store('bukti_files_pulang', 'public');
            $validated['bukti_file_pulang'] = $path;
        }

        $daerah_asal = $return_trip->daerah_asal;
        $tempat_asal = $return_trip->tempat_asal;
        $tempat_tujuan = $return_trip->tempat_tujuan;
        $tanggal_kembali_tiket = $return_trip->tanggal_kembali_tiket;

        if (!empty($validated['sppd_id'])) {
            $sppd = Sppd::find($validated['sppd_id']);
            if ($sppd) {
            $daerah_asal = $sppd->daerah;
            $tempat_asal = $sppd->tujuan;
            $tanggal_kembali_tiket = $sppd->tanggal_kembali;
            }
        }

        if (!empty($validated['departure_id'])) {
            $departure = Departure::find($validated['departure_id']);
            if($departure) {
                $tempat_tujuan = $departure->tempat_asal;
            }
        }

        $return_trip->update([
            ...$validated,
            'daerah_asal' => $daerah_asal,
            'tempat_asal' => $tempat_asal,
            'tempat_tujuan' => $tempat_tujuan,
            'tanggal_kembali_tiket' => $tanggal_kembali_tiket,
        ]);

        return response()->json([
            'message' => 'Data Kepulangan Berhasil Diperbarui',
            'data' => new ReturnTripResource($return_trip->load(['employeeSpt', 'sppd', 'spt', 'departure'])),
        ]);
    }
}
