<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\EmployeeResource;
use App\Http\Resources\EmployeeCollection;

class ApiEmployeeController extends Controller
{
    public function indexEmployee(Request $request)
    {
        $search = $request->get('search');

        $employees = Employee::query();

        if ($search) {
            $employees->where(function ($q) use ($search) {
                $q->where('nama_pegawai', 'like', "%$search%")
                ->orWhere('gelar_depan', 'like', "%$search%")
                ->orWhere('gelar_belakang', 'like', "%$search%")   
                ->orWhere('nip_nipppk', 'like', "%$search%")   
                ->orWhere('jabatan', 'like', "%$search%")   
                ->orWhere('golongan', 'like', "%$search%")   
                ->orWhere('pangkat', 'like', "%$search%");   
            });
        }
        $paginated = $employees->orderBy('created_at', 'desc')->paginate(10);
        return new EmployeeCollection($paginated);
    }


    public function showEmployee($id)
    {
        $employee = Employee::findOrFail($id);
        return new EmployeeResource($employee);
    }

    public function storeEmployee(Request $request)
    {
        $validated = $request->validate([
            'foto' => 'nullable|file|image|max:2048',
            'nip_nipppk' => 'nullable|string|size:18|unique:employees,nip_nipppk',
            'gelar_depan' => 'nullable|string|max:7',
            'nama_pegawai' => 'required|string|max:50',
            'gelar_belakang' => 'nullable|string|max:15',
            'jabatan' => 'nullable|string|max:50',
            'pangkat' => 'nullable|string|max:25',
            'golongan' => 'nullable|string|max:5',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('employees/foto', 'public');
        }

        $employee = Employee::create($validated);

        return response()->json([
            'message' => 'Data Pegawai Berhasil Ditambahkan',
            'data' => new EmployeeResource($employee)
        ], 201);
    }

    public function updateEmployee(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $validated = $request->validate([
            'foto' => 'nullable|file|image|max:2048',
            'nip_nipppk' => 'nullable|string|size:18|unique:employees,nip_nipppk,' . $employee->id,
            'gelar_depan' => 'nullable|string|max:7',
            'nama_pegawai' => 'required|string|max:50',
            'gelar_belakang' => 'nullable|string|max:15',
            'jabatan' => 'nullable|string|max:50',
            'pangkat' => 'nullable|string|max:25',
            'golongan' => 'nullable|string|max:5',
        ]);

        if ($request->hasFile('foto')) {
            if ($employee->foto && Storage::disk('public')->exists($employee->foto)) {
                Storage::disk('public')->delete($employee->foto);
            }
            $validated['foto'] = $request->file('foto')->store('employees/foto', 'public');
        }

        $employee->update($validated);

        return response()->json([
            'message' => 'Data Pegawai Berhasil Diperbarui',
            'data' => new EmployeeResource($employee)
        ]);
    }

    public function destroyEmployee($id)
    {
        $employee = Employee::findOrFail($id);

        if ($employee->foto && Storage::disk('public')->exists($employee->foto)) {
            Storage::disk('public')->delete($employee->foto);
        }

        $employee->delete();

        return response()->json([
            'message' => 'Data Pegawai Berhasil Dihapus'
        ]);
    }

    public function availableUser(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search');

        $employees = Employee::with('user')
            ->doesntHave('user'); // hanya pegawai yang belum punya user

        if ($search) {
            $employees->where(function($q) use ($search) {
                $q->where('nama_pegawai', 'like', "%$search%")
                ->orWhere('nip_nipppk', 'like', "%$search%");
            });
        }

        $paginated = $employees->paginate($perPage);

        return response()->json($paginated);
    }

    public function listForDropdown(Request $request)
    {
        $search = $request->get('search');

        $employees = Employee::query()
            ->when($search, function ($q) use ($search) {
                $q->where('nama_pegawai', 'like', "%$search%");
            })
            ->orderBy('nama_pegawai')
            ->limit(50)
            ->get();

        return response()->json($employees);
    }

}
