<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\Spt;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;

class RekapExport implements FromCollection, WithHeadings, WithCustomStartCell, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $rows;
    protected $year;

    public function __construct($year)
    {
        $this->rows = collect();
        $this->year = $year;
    }

    public function collection()
    {
        $no = 1;
        $spts = Spt::with(['employeesSpt', 'sppd', 'costs', 'departures', 'returns'])
            ->whereYear('tanggal_surat', $this->year)
            ->byUser()
            ->get();

        foreach ($spts as $spt) {
            foreach ($spt->employeesSpt as $employee) {
                $sppd = $spt->sppd;
                $cost = $spt->costs->where('employee_spt_id', $employee->id)->first();
                $departure = $spt->departures->where('employee_spt_id', $employee->id)->first();
                $return = $spt->returns->where('employee_spt_id', $employee->id)->first();

                $this->rows->push([
                    'No' => $no++,
                    // Surat Perintah Tugas
                    'Nama Pegawai' => $employee->nama_pegawai,
                    'NIP/NIPPPK' => $employee->nip_nipppk,
                    'Nomor Surat' => $spt->nomor_surat,
                    'Tanggal Surat' => $spt->tanggal_surat ? Carbon::parse($spt->tanggal_surat)->translatedFormat('j F Y') : null,
                    'Keperluan' => $spt->keperluan,
                    'Bidang' => $employee->bidang,
                    'Golongan' => $employee->golongan,
                    // SPPD
                    'Daerah' => $sppd?->daerah,
                    'Tujuan' => $sppd?->tujuan,
                    'Moda Transport' => $sppd?->moda_transport,
                    'Tanggal Berangkat' => $sppd?->tanggal_berangkat ? Carbon::parse($spt->tanggal_berangkat)->translatedFormat('j F Y') : null,
                    'Tanggal Kembali' => $sppd?->tanggal_kembali ? Carbon::parse($spt->tanggal_kembali)->translatedFormat('j F Y') : null,
                    'Lama Hari' => $sppd?->lama_hari,
                    // Rincian Biaya
                    'Uang Per Hari' => $cost?->uang_perhari,
                    'Total Uang Harian' => $cost?->total_uang_harian,
                    'Uang Representasi' => $cost?->uang_representasi,
                    'Nama Hotel' => $cost?->nama_hotel,
                    'Biaya Akomodasi' => $cost?->biaya_akomodasi,
                    'Biaya Kontribusi' => $cost?->biaya_kontribusi,
                    'Biaya Tiket Berangkat' => $cost?->biaya_tiket_berangkat,
                    'Biaya Tiket Kembali' => $cost?->biaya_tiket_kembali,
                    'Biaya Bantuan Transport' => $cost?->biaya_bantuan_transport,
                    'Biaya Taxi Berangkat' => $cost?->biaya_taxi_berangkat,
                    'Biaya Taxi Kembali' => $cost?->biaya_taxi_kembali,
                    'Biaya Travel' => $cost?->biaya_travel,
                    'Biaya Tidak Menginap' => $cost?->biaya_tidak_menginap,
                    'Total Biaya' => $cost?->total_biaya,
                    // Berangkat
                    'No Bukti Berangkat' => $departure?->no_bukti,
                    'Tanggal Bukti Berangkat' => $departure?->tanggal_bukti,
                    'No Tiket Berangkat' => $departure?->no_tiket_berangkat,
                    'Transportasi Berangkat' => $departure?->nama_transportasi,
                    'Tempat Asal (Berangkat)' => $departure?->tempat_asal,
                    'Daerah Tujuan (Berangkat)' => $departure?->daerah_tujuan,
                    'Tempat Tujuan (Berangkat)' => $departure?->tempat_tujuan,
                    'Tanggal Berangkat Tiket' => $departure?->tanggal_berangkat_tiket ? Carbon::parse($spt->tanggal_berangkat_tiket)->translatedFormat('j F Y') : null,
                    // Kembali
                    'No Bukti Kembali' => $return?->no_bukti,
                    'Tanggal Bukti Kembali' => $return?->tanggal_bukti,
                    'No Tiket Kembali' => $return?->no_tiket_kembali,
                    'Transportasi Kembali' => $return?->nama_transportasi,
                    'Daerah Asal (Pulang)' => $return?->daerah_asal,
                    'Tempat Asal (Pulang)' => $return?->tempat_asal,
                    'Tempat Tujuan (Pulang)' => $return?->tempat_tujuan,
                    'Tanggal Kembali Tiket' => $return?->tanggal_kembali_tiket ? Carbon::parse($spt->tanggal_kembali_tiket)->translatedFormat('j F Y') : null,

                    'Jumlah Dibayarkan' => (
                        ($cost?->total_uang_harian ?? 0) +
                        ($cost?->biaya_kontribusi ?? 0) +
                        ($cost?->biaya_tiket_berangkat ?? 0) +
                        ($cost?->biaya_tiket_kembali ?? 0) +
                        ($cost?->biaya_bantuan_transport ?? 0) +
                        ($cost?->biaya_taxi_berangkat ?? 0) +
                        ($cost?->biaya_taxi_kembali ?? 0) +
                        ($cost?->biaya_travel ?? 0)
                    ),
                ]);
            }
        }

        return $this->rows;
    }

     public function startCell(): string
    {
        return 'A4';
    }

     public function headings(): array
    {
        // Hanya header detail, kategori besar akan di-handle di AfterSheet
        return [
            'No',
            'Nama Pegawai', 
            'NIP/NIPPPK', 
            'Nomor Surat', 
            'Tanggal Surat',  
            'Keperluan Perjalanan Dinas',
            'Bidang/Unit', 
            'Golongan Pegawai', 
            'Daerah', 
            'Tujuan', 
            'Moda Transportasi', 
            'Tanggal Berangkat', 
            'Tanggal Kembali', 
            'Lama Hari',
            'Uang Per Hari', 
            'Total Uang Harian', 
            'Uang Representasi (Khusus Sekda, Pimpinan dan Anggota DPRD, dan Pejabat Es II )', 
            'Nama Penginapan/Hotel',
            'Biaya Akomodasi', 
            'Biaya Lain/Kontribusi', 
            'Biaya Tiket Maskapai/Kereta/Travel/Bis Berangkat', 
            'Biaya Tiket Maskapai/Kereta/Travel/Bis Kembali',
            'Biaya Bantuan Transport/BBM', 
            'Biaya Taxi Berangkat', 
            'Biaya Taxi Pulang', 
            'Biaya Travel',
            'Biaya Tidak Menginap (30%)', 
            'TOTAL BIAYA',
            'No Bukti Berangkat', 
            'Tanggal Bukti Berangkat', 
            'No Tiket Berangkat', 
            'Nama Maskapai/Kereta/Travel/Bis Berangkat',
            'Tempat Asal (Berangkat)', 
            'Daerah Tujuan (Berangkat)', 
            'Tempat Tujuan (Berangkat)', 
            'Tanggal Berangkat Tiket',
            'No Bukti Kembali', 
            'Tanggal Bukti Kembali', 
            'No Tiket Kembali', 
            'Nama Maskapai/Kereta/Travel/Bis Pulang',
            'Daerah Asal (Pulang)', 
            'Tempat Asal (Pulang)', 
            'Tempat Tujuan (Pulang)', 
            'Tanggal Kembali Tiket',
            'Jumlah Dibayarkan'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Judul & SKPD
                $sheet->mergeCells('A1:AS1');
                $sheet->setCellValue('A1', 'Rekap Perjalanan Dinas Luar Daerah TA ' . $this->year);
                $sheet->mergeCells('A2:AS2');
                $sheet->setCellValue('A2', 'SKPD……………..');

                $sheet->setCellValue('A3', 'No');
                $sheet->mergeCells('A3:A4');
                $sheet->getStyle('A3:A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A3:A4')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                
                // Kategori besar
                $sheet->setCellValue('B3', 'Surat Perintah Tugas');
                $sheet->mergeCells('B3:G3');

                $sheet->setCellValue('H3', 'SPPD');
                $sheet->mergeCells('H3:N3');

                $sheet->setCellValue('O3', 'Rincian Biaya');
                $sheet->mergeCells('O3:AB3');

                $sheet->setCellValue('AC3', 'Berangkat');
                $sheet->mergeCells('AC3:AJ3');

                $sheet->setCellValue('AK3', 'Kembali');
                $sheet->mergeCells('AK3:AR3');

                 // --- Styling umum ---
                // Bold & center judul, kategori, dan header detail
                $sheet->getStyle('A1:AP4')->getFont()->setBold(true);
                $sheet->getStyle('A1:AP4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A1:AP4')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                // Wrap header detail (baris 4)
                $sheet->getStyle('A4:AP4')->getAlignment()->setWrapText(true);

                // Tinggi baris
                $sheet->getRowDimension(1)->setRowHeight(25);
                $sheet->getRowDimension(2)->setRowHeight(20);
                $sheet->getRowDimension(3)->setRowHeight(22);
                $sheet->getRowDimension(4)->setRowHeight(35);

                // --- Warna coklat terang untuk header kategori (baris 3) ---
                $brown = 'FFD2B48C'; 
                foreach (['B3:G3', 'H3:M3', 'N3:AA3', 'AB3:AH3', 'AI3:AP3'] as $range) {
                    $sheet->getStyle($range)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB($brown);
                }

                $sheet->getStyle('C')->getNumberFormat()->setFormatCode('@');

                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();
                $range = 'A1:' . $highestColumn . $highestRow;

                $sheet->getStyle($range)->getBorders()->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                }
        ];
    }
}
