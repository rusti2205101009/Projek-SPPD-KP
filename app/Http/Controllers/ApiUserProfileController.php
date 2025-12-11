<?php

namespace App\Http\Controllers;

use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\UserProfileResource;
use App\Http\Resources\UserProfileCollection;

class ApiUserProfileController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $profiles = UserProfile::with('user.employee');

        if ($search) {
            $profiles->whereHas('user.employee', function ($q) use ($search) {
                $q->where('nama_pegawai', 'like', "%$search%")
                ->orWhere('nip_nipppk', 'like', "%$search%");
            });
        }

        $paginated = $profiles->orderBy('created_at', 'desc')->paginate(10);
        return new UserProfileCollection($paginated);
    }

    public function show()
    {
        $user = Auth::user();

        $profile = UserProfile::firstOrCreate(
            ['user_id' => $user->id]
        );
    
        return new UserProfileResource($profile->load('user.employee'));
    }

    // admin side
    public function showById($id)
    {
        $profile = UserProfile::with('user.employee')->findOrFail($id);

        $employee = $profile->user->employee;

        $namaLengkap = trim(
            ($employee->gelar_depan ? $employee->gelar_depan.' ' : '') .
            $employee->nama_pegawai .
            ($employee->gelar_belakang ? ', '.$employee->gelar_belakang : '')
        );

        return response()->json([
            'id' => $profile->id,
            'jenis_kelamin' => $profile->jenis_kelamin,
            'email' => $profile->email,
            'nohp' => $profile->nohp,
            'foto' => $profile->foto ? asset('storage/'.$profile->foto) : null,
            'nama_pegawai' => $namaLengkap,
            'nip_nipppk' => $profile->user->employee->nip_nipppk ?? '',
        ]);
    }

    public function update(Request $request, $id = null)
    {
        $authUser = Auth::user();

         if ($authUser->role === 'admin' && $id) {
            $profile = UserProfile::findOrFail($id);
        } else {
            $profile = UserProfile::firstOrCreate(['user_id' => $authUser->id]);
        }

        $data = $request->validate([
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'email' => 'nullable|string',
            'nohp' => 'nullable|string',
            'foto' => 'nullable|image|max:2048',
        ]);

        // Log::info('Update Profile Request for user_id '.$user->id, $data);

        foreach ($data as $key => $value) {
            if ($value === "") {
                $data[$key] = null;
            }
        }

        if ($request->hasFile('foto')) {
            if ($profile->foto) {
                Storage::disk('public')->delete($profile->foto);
            }
            $path = $request->file('foto')->store('profile_photos', 'public');
            $data['foto'] = $path;
        }

        $profile->update($data);
        // $updated = $profile->update($data);
        // Log::info('Profile update result for user_id '.$user->id, ['updated' => $updated, 'final_data' => $profile->toArray()]);


        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui',
            'data'    => new UserProfileResource($profile->load('user.employee')),
        ]);
    }
}