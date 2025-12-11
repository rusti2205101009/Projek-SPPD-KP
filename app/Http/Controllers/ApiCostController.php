<?php

namespace App\Http\Controllers;

use App\Models\Cost;
use Illuminate\Http\Request;
use App\Http\Resources\CostResource;
use App\Http\Resources\CostCollection;

class ApiCostController extends Controller
{
    public function indexCost(Request $request)
    {
        $search = $request->input('search');

        $costs = Cost::byUser()->with(['spt', 'sppd', 'employeeSpt']);

        if ($search) {
            $costs->whereHas('employeeSpt', function ($q) use ($search) {
                $q->where('nama_pegawai', 'like', "%$search%")
                ->orWhere('gelar_depan', 'like', "%$search%")
                ->orWhere('gelar_belakang', 'like', "%$search%")

                ->orWhereHas('spt', function ($q) use ($search) {
                    $q->where('nomor_surat', 'like', "%$search%");
                });
            });
        }

        $paginated = $costs->orderBy('created_at', 'desc')->paginate(10);
        return new CostCollection($paginated);
    }

    public function showCost($id)
    {
        $cost = Cost::byUser()->with(['employeeSpt', 'spt', 'sppd'])->findOrFail($id);
        return new CostResource($cost);
    }

    public function updateCost(Request $request, $id)
    {
        $cost = Cost::byUser()->findOrFail($id);

        $validated = $request->validate([
            'spt_id' => 'nullable|exists:spts,id',
            'sppd_id' => 'nullable|exists:sppds,id',
            'employee_spt_id' => 'nullable|exists:employee_spt,id',
            'uang_perhari' => 'nullable|numeric',
            'total_uang_harian' => 'nullable|numeric',
            'uang_representasi' => 'nullable|numeric',
            'nama_hotel' => 'nullable|string|max:50',
            'biaya_akomodasi' => 'nullable|numeric',
            'biaya_kontribusi' => 'nullable|numeric',
            'biaya_tiket_berangkat' => 'nullable|numeric',
            'biaya_tiket_kembali' => 'nullable|numeric',
            'biaya_bantuan_transport' => 'nullable|numeric',
            'biaya_taxi_berangkat' => 'nullable|numeric',
            'biaya_taxi_kembali' => 'nullable|numeric',
            'biaya_travel' => 'nullable|numeric',
            'biaya_tidak_menginap' => 'nullable|numeric',
            'total_biaya' => 'nullable|numeric',
            'bukti_file' => 'nullable|file|mimes:pdf|max:10048'
        ]);

        if ($request->hasFile('bukti_file')) {
            if ($cost->bukti_file && \Storage::exists($cost->bukti_file)) {
                \Storage::delete($cost->bukti_file);
            }

            $path = $request->file('bukti_file')->store('bukti_files', 'public');
            $validated['bukti_file'] = $path;
        }

        $cost->update($validated);

        return response()->json([
            'message' => 'Biaya berhasil diperbarui',
            'data' => new CostResource($cost->load(['employeeSpt', 'spt', 'sppd']))
        ]);
    }
}
