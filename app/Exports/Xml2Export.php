<?php
namespace App\Exports;

use App\Models\Transaksi;
use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithTitle;

class Xml2Export implements FromArray, WithHeadings, WithColumnFormatting, WithStyles, WithTitle

{
    private $start;
    private $end;
    private $rowNumber = 1;

    public function __construct($start, $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    public function headings(): array
    {
        return [
            'Baris',
            'Barang/Jasa',
            'Kode Barang Jasa',
            'Nama Barang/Jasa', //$jasa = (Kondisi) .()
            'Nama Satuan Ukur',
            'Harga Satuan',
            'Jumlah Barang Jasa',
            'Total Diskon',
            'DPP',
            'DPP Nilai Lain',
            'Tarif PPN',
            'PPN',
            'Tarif PPnBM',
            'PPnBM',
        ];
    }
     public function title(): string
    {
        return 'DetailFaktur'; // ganti sesuai kebutuhan
    }

   public function array(): array
{
    $data = [];
    $transaksis = Transaksi::with('pembayar', 'orderInfo.tarif.shipmentInfo', 'orderInfo.tarif.tujuan_lokasi', 'orderInfo.tarif.dari_lokasi', 'orderInfo.tarif.kondisiInfo')
        ->whereBetween('created_at', [$this->start, $this->end])
        ->orderBy('created_at')
        ->get();

    $rowNumber = 1;

    foreach ($transaksis as $item) {
        $kondisi = $item->orderInfo->tarif->kondisiInfo->id ?? null;
        $kodeBarangJasa = $item->keterangan;
        $Invoice = $item->invoice;
        $countInvoice = Order::where('invoice', $Invoice)->count();
        $tarifAsli = $item->orderInfo->tarif->tarif ?? 0;

        // Atur harga satuan: kurangi 500000 jika kondisi BUKAN 1 atau 6
        if (in_array($kondisi, [1, 6])) {
            $hargaSatuan = $tarifAsli - 500000; // Jangan sampai negatif
        } else {
            $hargaSatuan = $tarifAsli;
        }
        $dpp = number_format($item->sub_total, 2, '.', '');
        $ppn = number_format($item->ppn ?? 0, 2, '.', '');
        $NamaSatuan =  'UM.0030';
        $shipments = $item->orderInfo->tarif->shipmentInfo->id ?? 0;
        if ($shipments === 19 || $shipments === 13 || $shipments === 11 ){
             $NamaSatuan =  'UM.0033';
        }

        $row = [
            $rowNumber,
            'B',
            '060000',
            $kodeBarangJasa,
            $NamaSatuan,
            $hargaSatuan,
            $countInvoice,
            '0.00',
            $dpp,
            $dpp,
            12,
            $ppn,
            '0.00',
            '0.00',
        ];

        $data[] = $row;

        // Tambahkan baris kedua jika kondisi terpenuhi
        if (in_array($kondisi, [1, 6])) {
            $hargaSatuan = 500000;
            $dpp1 = $hargaSatuan * $countInvoice;
            $ppn1 = $dpp1 * 0.11;
            $extraRow = [
                $rowNumber,
                'B',
                '060000',
                'Jasa Ekspedisi', // misalnya beri pembeda
                $NamaSatuan,
                $hargaSatuan,
                $countInvoice,
                '0.00',
                $dpp1,
                $dpp1,
                12,
                $ppn1,
                '0.00',
                '0.00',
            ];
            $data[] = $extraRow;
        }

        $rowNumber++;
    }

    return $data;
}



    public function columnFormats(): array
    {
        return [
              'H' => NumberFormat::FORMAT_NUMBER_00, // DPP
             'I' => NumberFormat::FORMAT_NUMBER_00, // DPP
        'J' => NumberFormat::FORMAT_NUMBER_00, // DPP Nilai Lain
          'M' => NumberFormat::FORMAT_NUMBER_00, // DPP
        'N' => NumberFormat::FORMAT_NUMBER_00, // DPP Nilai Lain
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
