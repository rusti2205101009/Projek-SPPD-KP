<?php

namespace App\Http\Controllers;

use App\Exports\RekapExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function years()
    {
        $years = DB::table('spts')
            ->selectRaw('YEAR(tanggal_surat) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return response()->json($years);
    }

    public function fullRekap(Request $request)
    {
        $year = $request->query('year', date('Y'));
        // return Excel::download(new RekapExport, 'ful-rekap.xlsx');
        return Excel::download(new RekapExport($year), "rekap-{$year}.xlsx");
    }
}
