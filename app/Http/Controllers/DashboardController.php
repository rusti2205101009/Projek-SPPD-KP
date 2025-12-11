<?php

namespace App\Http\Controllers;

use App\Models\Spt;
use App\Models\Sppd;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
     public function stats()
    {
        return response()->json([
            'pegawai' => Employee::count(),
            'user'    => User::count(),
            'sppd'    => Sppd::byUser()->count(),
            'spt'     => Spt::byUser()->count(),
        ]);
    }
}
