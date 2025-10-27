<?php

namespace App\Exports;

use App\Models\Transaksi;
use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
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

    public function title(): string
    {
        return 'DetailFaktur';
    }

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

            // 🔹 Ambil semua order dengan invoice yang sama
            $orders = Order::with(['tarif.shipmentInfo', 'tarif.kondisiInfo'])
                ->where('invoice', $invoice)
                ->get();

            if ($orders->isEmpty()) {
                continue;
            }

            $isTambahanEkspedisi = false;
            $totalJumlahSemua = 0;
            $totalEkspedisi = 0;
            $totalPPNEkspedisi = 0;

            // 🔹 Kelompokkan data per keterangan
            $kodeBarangJasa = $item->keterangan ?? '';
            $keteranganList = array_filter(array_map('trim', explode(';', $kodeBarangJasa)));
            if (empty($keteranganList)) {
                $keteranganList = [''];
            }

            $kelompokData = [];

            foreach ($orders as $order) {
                $tarifAsli = $order->tarif->tarif ?? 0;
                $kondisi = $order->tarif->kondisiInfo->id ?? null;
                $shipmentId = $order->tarif->shipmentInfo->id ?? 0;

                $namaSatuan = in_array($shipmentId, [19, 13, 11]) ? 'UM.0033' : 'UM.0030';

                // Hitung harga & DPP
                $hargaPerOrder = in_array($kondisi, [1, 6])
                    ? $tarifAsli - 500000
                    : $tarifAsli;

                $dppOrder = $hargaPerOrder;
                $ppnOrder = $dppOrder * 0.11;

                // ambil keterangan sesuai urutan
                $keterangan = $keteranganList[min(count($kelompokData), count($keteranganList) - 1)];

                if (!isset($kelompokData[$keterangan])) {
                    $kelompokData[$keterangan] = [
                        'nama_satuan' => $namaSatuan,
                        'harga_satuan' => $hargaPerOrder,
                        'jumlah' => 0,
                        'total_dpp' => 0,
                        'total_ppn' => 0,
                    ];
                }

                $kelompokData[$keterangan]['jumlah']++;
                $kelompokData[$keterangan]['total_dpp'] += $dppOrder;
                $kelompokData[$keterangan]['total_ppn'] += $ppnOrder;
                $totalJumlahSemua++;

                // 🔹 Tambahan ekspedisi jika kondisi 1/6
                if (in_array($kondisi, [1, 6])) {
                    $isTambahanEkspedisi = true;
                    $totalEkspedisi += 500000;
                    $totalPPNEkspedisi += 500000 * 0.11;
                }
            }

            // 🔹 Tampilkan hasil per kelompok (keterangan)
            foreach ($kelompokData as $keterangan => $itemData) {
                $data[] = [
                    $rowNumber,
                    'B',
                    '060000',
                    $keterangan,
                    $itemData['nama_satuan'],
                    $itemData['harga_satuan'],
                    $itemData['jumlah'],
                    '0.00',
                    $itemData['total_dpp'],
                    $itemData['total_dpp'],
                    12,
                    $itemData['total_ppn'],
                    '0.00',
                    '0.00',
                ];
            }

            // 🔹 Tambahkan baris “Jasa Ekspedisi” terakhir jika perlu
            if ($isTambahanEkspedisi) {
                $data[] = [
                    $rowNumber,
                    'B',
                    '060000',
                    'Jasa Ekspedisi',
                    'UM.0030',
                    500000,
                    $totalJumlahSemua,
                    '0.00',
                    $totalEkspedisi * $totalJumlahSemua,
                    $totalEkspedisi * $totalJumlahSemua,
                    12,
                    $totalPPNEkspedisi * $totalJumlahSemua,
                    '0.00',
                    '0.00',
                ];
            }
             $rowNumber++;
        }

        $data[] = ['END'];

        return $data;
    }

    public function columnFormats(): array
    {
        return [
            'H' => NumberFormat::FORMAT_NUMBER_00,
            'I' => NumberFormat::FORMAT_NUMBER_00,
            'J' => NumberFormat::FORMAT_NUMBER_00,
            'M' => NumberFormat::FORMAT_NUMBER_00,
            'N' => NumberFormat::FORMAT_NUMBER_00,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:N1')->applyFromArray([
            'font' => [
                'name' => 'Calibri',
                'bold' => true,
                'size' => 10,
            ],
        ]);

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
