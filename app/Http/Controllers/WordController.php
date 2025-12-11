<?php

namespace App\Http\Controllers;

use App\Models\Spt;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\TemplateProcessor;

class WordController extends Controller
{
    public function cetakSpt($id)
    {
        // $user = Auth::user();
        // Log::info('Cetak SPT - User info', ['id' => $user->id, 'role' => $user->role]);
        Log::info("Cetak SPT - Mulai", ['spt_id' => $id]);
        
        $spt = Spt::byUser()->with('employeesSpt', 'headDivision', 'template')->findOrFail($id);
        // dd($spt->head_division_id, $spt->headDivision);
        
        // $templatePath= storage_path('app/templates/template-spt.docx');

        // $latestTemplate = Template::latest()->first(); // ambil template terbaru
        // if (!$latestTemplate || !Storage::exists($latestTemplate->file_path)) {
        //     abort(404, 'Template tidak ditemukan');
        // }

        // $templatePath = Storage::path($latestTemplate->file_path);
        // $templateProcessor = new TemplateProcessor($templatePath);

        Log::info("SPT ditemukan", ['spt_id' => $id, 'template_id' => $spt->template_id ?? null]);

        $template = $spt->template ?? Template::latest()->first();

        if (!$template) { // UPDATED
            Log::warning("Template tidak ditemukan sama sekali"); // UPDATED
            abort(404, 'Template tidak ditemukan'); // UPDATED
        }

        $disk = Storage::disk('public');

        if (!$disk->exists($template->file_path)) { // UPDATED
            Log::warning("Template tidak ditemukan di disk", [ // UPDATED
                'template_id' => $template->id, // UPDATED
                'expected_path' => $disk->path($template->file_path) // UPDATED
            ]); // UPDATED
            abort(404, 'Template tidak ditemukan'); // UPDATED
        }

        $templatePath = $disk->path($template->file_path);
        Log::info("Template path siap diproses", ['template_id' => $template->id, 'path' => $templatePath]);

        $templateProcessor = new TemplateProcessor($templatePath);

        // $pegawai = $spt->employees->first();
        // if ($pegawai) {
        //     $namaLengkap = trim(
        //         ($pegawai->pivot->gelar_depan ? $pegawai->pivot->gelar_depan.' ' : '') .
        //          $pegawai->pivot->nama_pegawai .
        //         ($pegawai->pivot->gelar_belakang ? ', '.$pegawai->pivot->gelar_belakang : '')
        //     );

        //     $templateProcessor->setValue('nama_pegawai', $namaLengkap);
        //     $templateProcessor->setValue('nip', $pegawai->pivot->nip);
        //     $templateProcessor->setValue('pangkat', $pegawai->pivot->pangkat);
        //     $templateProcessor->setValue('golongan', $pegawai->pivot->golongan);
        //     $templateProcessor->setValue('jabatan', $pegawai->pivot->jabatan);
        // } else {
        //     $templateProcessor->setValue('nama_pegawai', '-');
        //     $templateProcessor->setValue('nip', '-');
        //     $templateProcessor->setValue('pangkat', '-');
        //     $templateProcessor->setValue('golongan', '-');
        //     $templateProcessor->setValue('jabatan', '-');
        // }

        $pegawaiList = $spt->employeesSpt;
        $rows = [];
        // dd($pegawaiList->toArray());

        foreach ($pegawaiList as $i => $pegawai) {
            $namaLengkap = trim(
                ($pegawai->gelar_depan ? $pegawai->gelar_depan.' ' : '') .
                 strtoupper($pegawai->nama_pegawai) .
                ($pegawai->gelar_belakang ? ', '.$pegawai->gelar_belakang : '')
            );

            $rows[] = [
                'no'          => $i + 1,
                'nama_pegawai'=> $namaLengkap,
                'nip_nipppk'  => $pegawai->nip_nipppk,
                'pangkat'     => $pegawai->pangkat,
                'golongan'    => $pegawai->golongan,
                'jabatan'     => $pegawai->jabatan,
            ];
        }

        $templateProcessor->cloneBlock('pegawai_block', count($rows), true, false, $rows);


        $tanggalSurat = \Carbon\Carbon::parse($spt->tanggal_surat)
            ->locale('id')
            ->translatedFormat('d F Y');

        $templateProcessor->setValue('nomor_surat', $spt->nomor_surat);
        $templateProcessor->setValue('keperluan', $spt->keperluan);
        $templateProcessor->setValue('tanggal_surat', $tanggalSurat);

        // if ($spt->headDivision->ttd && Storage::disk('public')->exists($spt->headDivision->ttd)) {
        //     $templateProcessor->setImageValue('ttd', [
        //         'path' => Storage::disk('public')->path($spt->headDivision->ttd),
        //         'width' => 120,
        //         'height' => 80,
        //         'ratio' =>true
        //     ]);
        // } else {
        //     $templateProcessor->setValue('ttd', '');
        // }

        if ($spt->headDivision && $spt->headDivision->ttd && $disk->exists($spt->headDivision->ttd)) { // UPDATED
            $templateProcessor->setImageValue('ttd', [
                'path' => $disk->path($spt->headDivision->ttd), 
                'width' => 120,
                'height' => 80,
                'ratio' => true
            ]);
        } else {
            $templateProcessor->setValue('ttd', '');
        }

        $namaKepala = trim(
            ($spt->gelar_depan_kepala ? $spt->gelar_depan_kepala.' ' : '') .
              strtoupper($spt->nama_kepala) .
            ($spt->gelar_belakang_kepala ? ', '.$spt->gelar_belakang_kepala : '')
        );

        $templateProcessor->setValue('nama_kepala', $namaKepala);
        $templateProcessor->setValue('jabatan_kepala', strtoupper($spt->jabatan_kepala));
        $templateProcessor->setValue('pangkat_kepala', $spt->pangkat_kepala);
        $templateProcessor->setValue('nip_kepala', $spt->nip_kepala);

        $dir = storage_path('app/generated');
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        $fileName = 'spt_'.$spt->id.'.docx';
        $savePath = storage_path('app/generated/'.$fileName);
        $templateProcessor->saveAs($savePath);

        Log::info("File SPT berhasil disimpan", ['file_path' => $savePath]);

         return response()->file($savePath, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'Content-Disposition' => "attachment; filename=\"$fileName\""
        ]);
    }
}
