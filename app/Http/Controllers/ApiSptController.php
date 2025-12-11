<?php

namespace App\Http\Controllers;

use App\Models\Spt;
use App\Models\Cost;
use App\Models\Sppd;
use App\Models\Employee;
use App\Models\Template;
use App\Models\Departure;
use App\Models\ReturnTrip;
use App\Models\EmployeeSpt;
use App\Models\HeadDivision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\SptResource;
use App\Http\Resources\SptCollection;

class ApiSptController extends Controller
{
    public function indexSpt(Request $request)
    {
        $search = $request->get('search');

        $spts = Spt::byUser()->with(['headDivision', 'employeesSpt']);

        if ($search) {
            $spts->where(function ($query) use ($search) {
            $query->where('nomor_surat', 'like', "%$search%")
                ->orWhere('tanggal_surat', 'like', "%$search%")
                ->orWhere('keperluan', 'like', "%$search%")
                ->orWhere('nama_kepala', 'like', "%$search%")

                ->orWhereHas('employeesSpt', function ($q) use ($search) {
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

        $paginated = $spts->orderBy('created_at', 'desc')->paginate(10);
        return new SptCollection($paginated);
    }

    public function showSpt($id)
    {
        $spt = Spt::byUser()->with('headDivision', 'employeesSpt')->findOrFail($id);
        return new SptResource($spt);
    }

    public function storeSpt(Request $request)
    {
        $validated = $request->validate([
            'head_division_id' => 'nullable|exists:head_divisions,id',
            'nomor_surat' => 'required|string|max:50',
            'tanggal_surat' => 'required|date',
            'keperluan' => 'required|string',
            'pegawai_list' => 'required|array|min:1',
            'pegawai_list.*.employee_id' => 'required|exists:employees,id',
            'pegawai_list.*.bidang' => 'nullable|string|max:50',
        ]);

        DB::beginTransaction(); 

        try {
            $head = null;
            if (!empty($validated['head_division_id'])) {
                $head = HeadDivision::find($validated['head_division_id']);
            }

            $latestTemplate = Template::latest('id', 'desc')->first();
            \Log::info('Latest Template', ['template' => $latestTemplate]);

            if (!$latestTemplate) {
                return response()->json([
                    'message' => 'Template SPT belum tersedia, upload template terlebih dahulu.'
                ], 422);
            }
            
            $spt = Spt::create([
                'user_id' => $request->user()->id,
                'head_division_id' => $validated['head_division_id'] ?? null,
                'template_id' => $latestTemplate->id,
                'nomor_surat' => $validated['nomor_surat'],
                'tanggal_surat' => $validated['tanggal_surat'],
                'keperluan' => $validated['keperluan'],

                'nip_kepala' => $head->nip ?? '-',
                'gelar_depan_kepala' => $head->gelar_depan ?? null,
                'nama_kepala' => $head->nama_kepala ?? '-',
                'gelar_belakang_kepala' => $head->gelar_belakang ?? null,
                'jabatan_kepala' => $head->jabatan ?? '-',
                'pangkat_kepala' => $head->pangkat ?? '-',
                'golongan_kepala' => $head->golongan ?? '-',
            ]);

            \Log::info('SPT dibuat', ['spt_id' => $spt->id]);

            $sppd = Sppd::create([
                'spt_id' => $spt->id,
            ]);

             \Log::info('SPPD dibuat', ['sppd_id' => $sppd->id]);

            $listPegawai = Employee::whereIn(
                'id', collect($validated['pegawai_list'])->pluck('employee_id')->toArray()
            )->get();

            $bidangMap = collect($validated['pegawai_list'])->mapWithKeys(function ($item) {
                return [$item['employee_id'] => $item['bidang'] ?? null];
            });

            $pivotIds = [];

            foreach ($validated['pegawai_list'] as $item) {
                $pegawai = Employee::findOrFail($item['employee_id']);

                $pivot = EmployeeSpt::create([
                    'employee_id' => $pegawai->id,
                    'spt_id' => $spt->id,
                    'gelar_depan' => $pegawai->gelar_depan,
                    'nama_pegawai' => $pegawai->nama_pegawai,
                    'gelar_belakang' => $pegawai->gelar_belakang,
                    'nip_nipppk' => $pegawai->nip_nipppk,
                    'jabatan' => $pegawai->jabatan,
                    'pangkat' => $pegawai->pangkat,
                    'golongan' => $pegawai->golongan,
                    'bidang' => $bidangMap[$pegawai->id] ?? null,
                ]);

                \Log::info('Pivot EmployeeSpt dibuat', ['pivot_id' => $pivot->id, 'pegawai_id' => $pegawai->id]);

                $pivotIds[] = $pivot->id;
                
                $cost = Cost::create([
                    'employee_spt_id' => $pivot->id,
                    'spt_id' => $spt->id,
                    'sppd_id' => $sppd->id,
                ]);

                 \Log::info('Cost dibuat', ['cost_id' => $cost->id, 'employee_spt_id' => $cost->employee_spt_id]);

                $departure = Departure::create([
                    'employee_spt_id' => $pivot->id,
                    'spt_id' => $spt->id,
                    'sppd_id' => $sppd->id,
                ]);

                \Log::info('Departure dibuat', ['departure_id' => $departure->id, 'employee_spt_id' => $departure->employee_spt_id]);

                $returnTrip = ReturnTrip::create([
                    'employee_spt_id' => $pivot->id,
                    'spt_id' => $spt->id,
                    'sppd_id' => $sppd->id,
                    'departure_id' => $departure->id,
                ]);

                \Log::info('ReturnTrip dibuat', ['return_trip_id' => $returnTrip->id, 'employee_spt_id' => $returnTrip->employee_spt_id]);
            }

            DB::commit();

            return response()->json([
                'message' => 'STP berhasil dibuat',
                'data' => new SptResource($spt->load(['headDivision', 'employeesSpt']))
            ], 201);
            
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Gagal membuat Surat Perintah Tugas',
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ], 500);
        }        
    }

    public function updateSpt(Request $request, $id)
    {
        $spt = Spt::byUser()->findOrFail($id);

        $validated = $request->validate([
            'head_division_id' => 'nullable|exists:head_divisions,id',
            'nomor_surat' => 'required|string|max:50',
            'tanggal_surat' => 'required|date',
            'keperluan' => 'required|string',
            'pegawai_list' => 'required|array|min:1',
            'pegawai_list.*.employee_id' => 'required|exists:employees,id',
            'pegawai_list.*.bidang' => 'nullable|string|max:50',
        ]);

        DB::beginTransaction();

        try {
            $head = null;
            if (!empty($validated['head_division_id'])) {
                $head = HeadDivision::find($validated['head_division_id']);
            }

            $spt->update([
                'head_division_id' => $validated['head_division_id'] ?? null,
                'nomor_surat' => $validated['nomor_surat'],
                'tanggal_surat' => $validated['tanggal_surat'],
                'keperluan' => $validated['keperluan'],

                'nip_kepala' => $head->nip ?? '-',
                'gelar_depan_kepala' => $head->gelar_depan ?? null,
                'nama_kepala' => $head->nama_kepala ?? '-',
                'gelar_belakang_kepala' => $head->gelar_belakang ?? null,
                'jabatan_kepala' => $head->jabatan ?? '-',
                'pangkat_kepala' => $head->pangkat ?? '-',
                'golongan_kepala' => $head->golongan ?? '-',
            ]);

            $oldPivotIds = EmployeeSpt::where('spt_id', $spt->id)->pluck('id');

            if ($oldPivotIds->isNotEmpty()) {
                Cost::whereIn('employee_spt_id', $oldPivotIds)->delete();
                Departure::whereIn('employee_spt_id', $oldPivotIds)->delete();
                ReturnTrip::whereIn('employee_spt_id', $oldPivotIds)->delete();
            }

            EmployeeSpt::where('spt_id', $spt->id)->delete();
            
            $sppd = Sppd::firstOrCreate(
                ['spt_id' => $spt->id],
            );

            $employeeIds = collect($validated['pegawai_list'])->pluck('employee_id');
            $employees = Employee::whereIn('id', $employeeIds)->get()->keyBy('id');

            $bidangMap = collect($validated['pegawai_list'])->mapWithKeys(function ($item) {
                return [$item['employee_id'] => $item['bidang'] ?? null];
            });

            foreach ($validated['pegawai_list'] as $item) {
                $pegawai = Employee::findOrFail($item['employee_id']);

                 $pivot = EmployeeSpt::create([
                    'spt_id' => $spt->id,
                    'employee_id' => $pegawai->id,
                    'gelar_depan' => $pegawai->gelar_depan,
                    'nama_pegawai' => $pegawai->nama_pegawai,
                    'gelar_belakang' => $pegawai->gelar_belakang,
                    'nip_nipppk' => $pegawai->nip_nipppk,
                    'jabatan' => $pegawai->jabatan,
                    'pangkat' => $pegawai->pangkat,
                    'golongan' => $pegawai->golongan,
                    'bidang' => $bidangMap[$pegawai->id] ?? null,
                ]);

                Cost::Create(
                    [
                        'employee_spt_id' => $pivot->id,
                        'spt_id'     => $spt->id,
                        'sppd_id'    => $sppd->id,
                    ]
                );

                $departure = Departure::Create(
                    [
                        'employee_spt_id' => $pivot->id,
                        'spt_id'     => $spt->id,
                        'sppd_id'    => $sppd->id,
                    ]
                );

                ReturnTrip::Create(
                    [
                        'employee_spt_id' => $pivot->id,
                        'spt_id'     => $spt->id,
                        'sppd_id'    => $sppd->id,
                        'departure_id'  => $departure->id,
                    ]
                );
            }

        DB::commit();

        return response()->json([
            'message' => 'STP berhasil diperbarui',
            'data' => new SptResource($spt->load(['headDivision', 'employeesSpt']))
        ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            
            return response()->json([
                'message' => 'Gagal membuat STP',
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ], 500);    
        }
    }

    public function destroySpt($id)
    {
        $spt = Spt::byUser()->findOrFail($id);

        DB::beginTransaction();

        try {
            DB::table('employee_spt')->where('spt_id', $spt->id)->delete();

            Cost::where('spt_id', $spt->id)->delete();

            $sppds = Sppd::where('spt_id', $spt->id)->get();

            foreach ($sppds as $sppd) {
                Departure::where('sppd_id', $sppd->id)->delete();
                ReturnTrip::where('sppd_id', $sppd->id)->delete();

                $sppd->delete();
            }

            $spt->delete();

            DB::commit();

            return response()->json([
                'message' => 'Surat Perintah Tugas dan seluruh relasi berhasil dihapus'
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Gagal menghapus Surat Perintah Tugas',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
