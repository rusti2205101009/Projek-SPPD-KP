<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'nip_nipppk' => ['required', 'string', 'size:18'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt(['nip_nipppk' => $credentials['nip_nipppk'], 'password' => $credentials['password']])) {
           throw ValidationException::withMessages([
            'nip_nipppk' => ['NIP/NIPPPK atau password anda salah'],
           ]);
        }

        $user = Auth::user();

         /**@var \App\Models\User $user */
        $user->tokens()->delete();

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token,
            'role' => $user->role,
            'redirect_to' => $user->role === 'admin' ? '/admin/dashboard' : '/user/dashboard',
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout Berhasil']);
    }

    public function me(Request $request)
    {
        // return response()->json(Auth::user());

        $user = $request->user()->load(['employee', 'profile']); 

        return response()->json([
            'id'   => $user->id,
            'nip_nipppk'  => $user->employee?->nip_nipppk,
            'nama_pegawai' => $user->employee?->nama_pegawai,
            'foto' => $user->foto 
                ? asset('storage/' . $user->foto) 
                : null,
            'role' => $user->role,
        ]);
    }

    public function updatePhoto(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'foto' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($user->foto) {
            \Storage::disk('public')->delete($user->foto);
        }
        
        $user->foto = $request->file('foto')->store('user_fotos', 'public');
        $user->save();

        return response()->json([
            'message' => 'Foto berhasil diperbarui',
            'foto' => asset('storage/' . $user->foto)
        ]);
    }
}
