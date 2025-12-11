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
        Log::info("Cetak SPT - Mulai", ['spt_id' => $id]);
        
        $spt = Spt::byUser()->with('employeesSpt', 'headDivision', 'template')->findOrFail($id);
        
        Log::info("SPT ditemukan", ['spt_id' => $id, 'template_id' => $spt->template_id ?? null]);

        $template = $spt->template ?? Template::latest()->first();

        if (!$template) {
            Log::warning("Template tidak ditemukan sama sekali"); 
            abort(404, 'Template tidak ditemukan'); 
        }

        $disk = Storage::disk('public');

        if (!$disk->exists($template->file_path)) { 
            Log::warning("Template tidak ditemukan di disk", [ 
                'template_id' => $template->id, 
                'expected_path' => $disk->path($template->file_path) 
            ]); 
            abort(404, 'Template tidak ditemukan'); 
        }

        $templatePath = $disk->path($template->file_path);
        Log::info("Template path siap diproses", ['template_id' => $template->id, 'path' => $templatePath]);

        $templateProcessor = new TemplateProcessor($templatePath);

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
