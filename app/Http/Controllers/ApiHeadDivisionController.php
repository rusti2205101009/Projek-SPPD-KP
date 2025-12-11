<?php

namespace App\Http\Controllers;

use App\Models\HeadDivision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\HeadDivisionResource;
use App\Http\Resources\HeadDivisionCollection;

class ApiHeadDivisionController extends Controller
{
    public function indexHeadDivision(Request $request)
    {
        $search = $request->get('search');

        $headDivision = HeadDivision::query();

         if ($search) {
            $headDivision->where(function ($q) use ($search) {
                $q->where('nama_kepala', 'like', "%$search%")
                ->orWhere('gelar_depan', 'like', "%$search%")
                ->orWhere('gelar_belakang', 'like', "%$search%")
                ->orWhere('nip', 'like', "%$search%")   
                ->orWhere('jabatan', 'like', "%$search%")   
                ->orWhere('golongan', 'like', "%$search%")   
                ->orWhere('pangkat', 'like', "%$search%");
            });
        }

        $paginated = $headDivision->orderBy('created_at', 'desc')->paginate(10);
        return new HeadDivisionCollection($paginated);
    }

    public function showHeadDivision($id)
    {
        $headDivision = HeadDivision::findOrFail($id);
        return new HeadDivisionResource($headDivision);
    }

    public function storeHeadDivision(Request $request)
    {
        $validated = $request->validate([
            'foto' => 'nullable|file|image|max:2048',
            'nip' => 'required|string|size:18|unique:head_divisions,nip',
            'gelar_depan' => 'nullable|string|max:7',
            'nama_kepala' => 'required|string|max:50',
            'gelar_belakang' => 'nullable|string|max:15',
            'jabatan' => 'required|string|max:50',
            'pangkat' => 'required|string|max:25',
            'golongan' => 'required|string|max:5',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('head_divisions/foto_kepala', 'public');
        }

        if ($request->hasFile('ttd')) {
            $validated['ttd'] = $request->file('ttd')->store('head_divisions/ttd_kepala', 'public');
        }

        $headDivision = HeadDivision::create($validated);

        return response()->json([
            'message' => 'Data Pimpinan Berhasil Ditambahkan',
            'data' => new HeadDivisionResource($headDivision)
        ], 201);
    }

    public function updateHeadDivision(Request $request, $id)
    {
        $headDivision = HeadDivision::findOrFail($id);

        $validated = $request->validate([
            'foto' => 'nullable|file|image|max:2048',
            'nip' => 'required|string|size:18|unique:head_divisions,nip,' . $headDivision->id,
            'gelar_depan' => 'nullable|string|max:7',
            'nama_kepala' => 'required|string|max:50',
            'gelar_belakang' => 'nullable|string|max:15',
            'jabatan' => 'required|string|max:50',
            'pangkat' => 'required|string|max:25',
            'golongan' => 'required|string|max:5',
        ]);

        if ($request->hasFile('foto')) {
            if ($headDivision->foto && Storage::disk('public')->exists($headDivision->foto)) {
                Storage::disk('public')->delete($headDivision->foto);
            }
            $validated['foto'] = $request->file('foto')->store('head_divisions/foto_kepala', 'public');
        }

        if ($request->hasFile('ttd')) {
            if ($headDivision->ttd && Storage::disk('public')->exists($headDivision->ttd)) {
                Storage::disk('public')->delete($headDivision->ttd);
            }
            $validated['ttd'] = $request->file('ttd')->store('head_divisions/ttd_kepala', 'public');
        }

        $headDivision->update($validated);

        return response()->json([
            'message' => 'Data Pimpinan Berhasil Diperbarui',
            'data' => new HeadDivisionResource($headDivision)
        ]);
    }

    public function destroyHeadDivision($id)
    {
        $headDivision = HeadDivision::findOrFail($id);

        if ($headDivision->foto && Storage::disk('public')->exists($headDivision->foto)) {
            Storage::disk('public')->delete($headDivision->foto);
        }

        $headDivision->delete();

        return response()->json([
            'message' => 'Data Kepala Berhasil Dihapus'
        ]);
    }
    
}
