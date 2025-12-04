<?php

namespace App\Exports;

use App\Models\Transaksi;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class LaporanPPNExport implements 
    FromQuery, WithMapping, WithHeadings, 
    WithColumnFormatting, ShouldAutoSize
{
    private $start;
    private $end;
    private $iteration = 0;

    public function __construct($start, $end)
    {
        $this->start = $start;
        $this->end   = $end;
    }

    public function query()
    {
        return Transaksi::with([
            'pembayar:id,npwp,nik,nama,nama_npwp,alamat_npwp'
        ])
        ->whereBetween('created_at', [$this->start, $this->end])
        ->orderBy('created_at');
    }

    public function headings(): array
    {
        return [
            'No',
            'Invoice',
            'NPWP',
            'NIK',
            'Nama',
            'Nama NPWP',
            'Alamat NPWP',
            'Tanggal Faktur',
            'Tujuan',
            'Uraian',
            'Daftar Faktur Pajak',
            'Sub Total',
            'PPN',
            'Total',
            'PPH',
            'No.JOB',
            'No BUPOT',
            'Masa BUPOT',
            'BUPOT',
            'Selisih BUPOT',
        ];
    }

    public function map($item): array
    {
        $this->iteration++;

        return [
            $this->iteration,
            $item->invoice,
            $item->pembayar->npwp,
            $item->pembayar->nik,
            $item->pembayar->nama,
            $item->pembayar->nama_npwp,
            $item->pembayar->alamat_npwp,
            $item->created_at->format('d/m/y'),
            $item->tujuan,
            $item->keterangan,
            $item->nsfp,
            round($item->sub_total),
            round($item->ppn),
            round($item->ppn + $item->sub_total),
            round($item->pph),
            $item->no_job(),
            $item->no_bupot,
            $item->masa_bupot,
            round($item->bupot),
            $item->selisih_bupot,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'L' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'M' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'N' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'O' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'S' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }
}
