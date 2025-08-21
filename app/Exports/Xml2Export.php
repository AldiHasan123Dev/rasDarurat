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
    $transaksis = Transaksi::with([
            'pembayar',
            'orderInfo.tarif.shipmentInfo',
            'orderInfo.tarif.tujuan_lokasi',
            'orderInfo.tarif.dari_lokasi',
            'orderInfo.tarif.kondisiInfo'
        ])
        ->whereBetween('created_at', [$this->start, $this->end])
        ->orderBy('created_at')
        ->get();

    $rowNumber = 1;

    foreach ($transaksis as $item) {
        $Invoice = $item->invoice;

        // Ambil semua order per invoice, group by tarif_id
        $orders = Order::with('tarif.shipmentInfo', 'tarif.kondisiInfo')
            ->where('invoice', $Invoice)
            ->get()
            ->groupBy('tarif_id');

        foreach ($orders as $tarifId => $ordersGroup) {
            $firstOrder = $ordersGroup->first();
            $kondisi    = $firstOrder->tarif->kondisiInfo->id ?? null;
            $kodeBarangJasa = $item->keterangan;
            $tarifAsli  = $firstOrder->tarif->tarif ?? 0;
            $jumlahContainer = $ordersGroup->count();

            // Atur harga satuan
            if (in_array($kondisi, [1, 6])) {
                $hargaSatuan = $tarifAsli - 500000;
                $dpp = $hargaSatuan * $jumlahContainer;
                $ppn = number_format($dpp * 0.11, 2, '.', ''); 
            } else {
                $hargaSatuan = $tarifAsli;
                $dpp = $hargaSatuan * $jumlahContainer;
                $ppn = number_format($dpp * 0.11, 2, '.', '');
            }

            // Tentukan Nama Satuan berdasarkan shipment
            $NamaSatuan = 'UM.0030';
            $shipments = $firstOrder->tarif->shipmentInfo->id ?? 0;
            if (in_array($shipments, [19, 13, 11])) {
                $NamaSatuan = 'UM.0033';
            }

            // Row utama
            $row = [
                $rowNumber, // nomor sama untuk semua tarif_id dalam invoice ini
                'B',
                '060000',
                $kodeBarangJasa,
                $NamaSatuan,
                $hargaSatuan,
                $jumlahContainer,
                '0.00',
                $dpp,
                $dpp,
                12,
                $ppn,
                '0.00',
                '0.00',
            ];
            $data[] = $row;

            // Tambahkan baris kedua jika kondisi [1,6]
            if (in_array($kondisi, [1, 6])) {
                $hargaSatuan = 500000;
                $dpp1 = $hargaSatuan * $jumlahContainer;
                $ppn1 = number_format($dpp1 * 0.11, 2, '.', '');
                $extraRow = [
                    $rowNumber,
                    'B',
                    '060000',
                    'Jasa Ekspedisi',
                    $NamaSatuan,
                    $hargaSatuan,
                    $jumlahContainer,
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
        }

        // setelah selesai semua tarif_id di invoice ini → naikkan nomor
        $rowNumber++;
    }

    $data[] = ['END'];
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
