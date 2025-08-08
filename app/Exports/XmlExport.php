<?php
namespace App\Exports;

use App\Models\Transaksi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithTitle;

class XmlExport implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting, WithStyles, WithTitle
{
    private $start;
    private $end;
    private $rowNumber = 1;

    public function __construct($start, $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

   public function collection()
{
    $transaksis = Transaksi::with('pembayar')
        ->whereBetween('created_at', [$this->start, $this->end])
        ->orderBy('created_at')
        ->get()
        ->values();

    // Simpan flag agar tahu kita akan memproses baris END di map()
    $this->withEnd = true;

    // Tambahkan placeholder dummy object untuk "END"
    $transaksis->push((object)['is_end' => true]);

    return $transaksis;
}


    public function headings(): array
    {
        return [
            'Baris',
            'Tanggal Faktur',
            'Jenis Faktur',
            'Kode Transaksi',
            'Keterangan Tambahan',
            'Dokumen Pendukung',
            'Periode Dok Pendukung',
            'Refrensi',
            'Cap Fasilitas',
            'ID TKU Penjual',
            'NPWP/NIK Pembeli',
            'Jenis ID Pembeli',
            'Negara Pembeli',
            'Nomor Dokumen Pembeli',
            'Nama Pembeli',
            'Alamat Pembeli',
            'Email Pembeli',
            'ID TKU Pembeli',
        ];
    }

     public function title(): string
    {
        return 'Faktur'; // ganti sesuai kebutuhan
    }
   public function map($item): array
{
     if (isset($item->is_end) && $item->is_end) {
        return ['END'];
    }
    $npwpPenjual = "0753461920614000000000"; // tanpa petik satu

    $npwpOrNik = $item->pembayar->nik === '-' 
        ? str_replace('.', '', $item->pembayar->npwp) 
        : $item->pembayar->nik;

    // Hapus karakter 'x' jika ada
    $npwpOrNik = str_replace('x', '', $npwpOrNik);
    
    $npwpOrNik1 = (string) $npwpOrNik;

    if (strlen($npwpOrNik1) === 16) {
        $npwpOrNik1 = str_pad($npwpOrNik, 22, '0', STR_PAD_RIGHT);
    }

    
    if ($npwpOrNik === '0000000000000000') {
    $jenisId = 'Other ID';
    } else {
        $jenisId = 'TIN';
    }
    
    $NomorDokPembeli = '-';
    if ($jenisId === 'Other ID') {
         $NomorDokPembeli = $npwpOrNik;
    } else {
         $NomorDokPembeli = '-';
    }



    return [
        $this->rowNumber++,
        date('d/m/y', strtotime($item->created_at)),
        'Normal',
        '05',
        '',
        '',
        '',
        $item->invoice,
        '',
        (string) $npwpPenjual,
        (string) $npwpOrNik,
        $jenisId,
        'IDN',
        $NomorDokPembeli,
        $item->pembayar->nama,
        $item->pembayar->alamat_npwp,
        $item->pembayar->email ?? '-',
        (string) $npwpOrNik1,
    ];
}


    public function columnFormats(): array
    {
        return [
            'K' => NumberFormat::FORMAT_TEXT, // Kolom NPWP/NIK Pembeli
            'J' => NumberFormat::FORMAT_TEXT, // Kolom ID TKU Penjual
            'R' => NumberFormat::FORMAT_TEXT, // Kolom ID TKU Pembeli
        ];
    }
    public function styles(Worksheet $sheet)
{
    // Styling header row
    $sheet->getStyle('A1:R1')->applyFromArray([
        'font' => [
            'name' => 'Calibri',
            'bold' => true,
            'size' => 10,
        ],
    ]);

    // Styling data rows
    $highestRow = $sheet->getHighestRow();
    $sheet->getStyle("A2:R{$highestRow}")->applyFromArray([
        'font' => [
            'name' => 'Segoe UI',
            'size' => 11,
        ],
    ]);

    return [];
}

}
