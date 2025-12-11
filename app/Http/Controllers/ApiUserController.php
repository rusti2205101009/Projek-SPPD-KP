<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserCollection;

class ApiUserController extends Controller
{
    public function indexUser(Request $request)
    {
        $search = $request->get('search');

        $users = User::with('employee');

        if ($search) {
            $users->whereHas('employee', function ($q) use ($search) {
                $q->where('nama_pegawai', 'like', "%$search%")
                ->orWhere('nip_nipppk', 'like', "%$search%");
            });
        }

        $paginated = $users->orderBy('created_at', 'desc')->paginate(10);
        return new UserCollection($paginated);
    }

    public function showUser($id)
    {
        $user = User::with(['employee'])->findOrFail($id);
        return new UserResource($user);
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'nip_nipppk' => 'required|string|size:18|unique:users,nip_nipppk',
            'password' => 'required|string|min:6',
            'role' => ['required', Rule::in(['admin', 'user'])],
            'employee_id' => 'nullable|exists:employees,id',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('user_fotos', 'public');
        }

        $user = User::create($validated);

        // UserProfile::create([
        //     'user_id' => $user->id,
        // ]);

        return response()->json([
            'message' => 'User Berhasil Ditambahkan',
            'data' => new UserResource($user->load('employee')),
        ], 201);
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request -> validate([
            'nip_nipppk' => ['required', 'string', 'size:18', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:6',
            'role' => ['required', Rule::in(['admin', 'user'])],
            'employee_id' => 'nullable|exists:employees,id',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if (isset($validated['password']) && $validated['password'] !== null) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        if ($request->hasFile('foto')) {
            if ($user->foto) {
                \Storage::disk('public')->delete($user->foto);
            }
            $validated['foto'] = $request->file('foto')->store('user_fotos', 'public');
        }

        $user->update($validated);

        return response()->json([
            'message' => 'User Berhasil Diperbarui',
            'data' => new UserResource($user->load('employee')),
        ]);
    }

    public function destroyUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User Berhasil Dihapus']);
    }

    public function updatePass(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password lama tidak sesuai.'
            ], 422);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diperbarui.'
        ]);
    }
}
