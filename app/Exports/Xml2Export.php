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

    public function __construct($start, $end)
    {
        $this->start = $start;
        $this->end   = $end;
    }

    /**
     * Judul sheet
     */
    public function title(): string
    {
        return 'DetailFaktur';
    }

    /**
     * Header kolom
     */
    public function headings(): array
    {
        return [
            'Baris',
            'Barang/Jasa',
            'Kode Barang Jasa',
            'Nama Barang/Jasa',
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

    /**
     * Ambil data untuk export
     */
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
            $invoice = $item->invoice;

            // Ambil semua order per invoice, group by tarif_id
            $orders = Order::with(['tarif.shipmentInfo', 'tarif.kondisiInfo'])
                ->where('invoice', $invoice)
                ->get()
                ->groupBy('tarif_id');

            foreach ($orders as $tarifId => $ordersGroup) {
                $firstOrder       = $ordersGroup->first();
                $kondisi          = $firstOrder->tarif->kondisiInfo->id ?? null;
                $kodeBarangJasa   = $item->keterangan ?? '';
                $tarifAsli        = $firstOrder->tarif->tarif ?? 0;
                $jumlahContainer  = $ordersGroup->count();

                // Atur harga satuan
                if (in_array($kondisi, [1, 6])) {
                    $hargaSatuan = $tarifAsli - 500000;
                } else {
                    $hargaSatuan = $tarifAsli;
                }

                $dpp = $hargaSatuan * $jumlahContainer;
                $ppn = $dpp * 0.11;

                // Tentukan Nama Satuan berdasarkan shipment
                $NamaSatuan = 'UM.0030';
                $shipmentId = $firstOrder->tarif->shipmentInfo->id ?? 0;
                if (in_array($shipmentId, [19, 13, 11])) {
                    $NamaSatuan = 'UM.0033';
                }

                /**
                 * Pecah keterangan menjadi beberapa baris
                 * Jika ada tanda ";" → buat baris baru untuk setiap keterangan
                 */
               $keteranganList = array_filter(explode(';', $kodeBarangJasa));
               $keterangan = trim(reset($keteranganList)); // ambil hanya satu keterangan

                $data[] = [
                    $rowNumber,
                    'B',
                    '060000',
                    $keterangan,
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
                // Jika kondisi [1,6], tambahkan baris ekstra
                if (in_array($kondisi, [1, 6])) {
                    $hargaSatuanTambahan = 500000;
                    $dppTambahan = $hargaSatuanTambahan * $jumlahContainer;
                    $ppnTambahan = $dppTambahan * 0.11;

                    $data[] = [
                        $rowNumber,
                        'B',
                        '060000',
                        'Jasa Ekspedisi',
                        $NamaSatuan,
                        $hargaSatuanTambahan,
                        $jumlahContainer,
                        '0.00',
                        $dppTambahan,
                        $dppTambahan,
                        12,
                        $ppnTambahan,
                        '0.00',
                        '0.00',
                    ];
                }
            }

            // Setelah selesai semua tarif_id dalam invoice ini → naikkan nomor
            $rowNumber++;
        }

        // Tambahkan penanda akhir data
        $data[] = ['END'];

        return $data;
    }

    /**
     * Format kolom angka
     */
    public function columnFormats(): array
    {
        return [
            'H' => NumberFormat::FORMAT_NUMBER_00, // Total Diskon
            'I' => NumberFormat::FORMAT_NUMBER_00, // DPP
            'J' => NumberFormat::FORMAT_NUMBER_00, // DPP Nilai Lain
            'M' => NumberFormat::FORMAT_NUMBER_00, // Tarif PPnBM
            'N' => NumberFormat::FORMAT_NUMBER_00, // PPnBM
        ];
    }

    /**
     * Styling header dan isi
     */
    public function styles(Worksheet $sheet)
    {
        // Styling header row
        $sheet->getStyle('A1:N1')->applyFromArray([
            'font' => [
                'name' => 'Calibri',
                'bold' => true,
                'size' => 10,
            ],
        ]);

        // Styling data rows
        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle("A2:N{$highestRow}")->applyFromArray([
            'font' => [
                'name' => 'Segoe UI',
                'size' => 11,
            ],
        ]);

        return [];
    }
}
