<?php

namespace App\Exports;

use App\Models\COA;
use App\Models\Jurnal;
use Illuminate\Contracts\Queue\ShouldQueue as QueueShouldQueue;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Carbon;

class JurnalCoaQueryExport implements FromQuery, WithMapping, WithHeadings, WithChunkReading, QueueShouldQueue, ShouldAutoSize, WithEvents
{
    private $month;
    private $year;
    private $coaId;
    private $tipe = 'D';
    private $startSaldo = 0;
    private $runningSaldo = 0;
    private $last;
    private $sumDebitMonth = 0;
    private $sumCreditMonth = 0;

    public function __construct(int $coa, int $year, int $month)
    {
        $this->coaId = $coa;
        $this->month = $month;
        $this->year = $year;

        $c = COA::find($this->coaId);
        if (! $c) {
            return;
        }

        if (substr($c->kode, 0, 1) == '2' || substr($c->kode, 0, 1) == '3' || substr($c->kode, 0, 1) == '5') {
            $this->tipe = 'C';
        }

        $ca = new Carbon($this->year.'-'.sprintf('%02d',$this->month).'-01');
        $this->last = $ca->subMonth()->endOfMonth()->format('Y-m-d');

        $kode_awal = substr($c->kode, 0, 1);
        if (in_array($kode_awal, ['5', '6', '7'])) {
            $this->startSaldo = 0;
            $this->runningSaldo = 0;
        } else {
            $rangeStart = '2022-12-01';
            $rangeEnd = $this->last;
            $totals = Jurnal::where('coa_id', $this->coaId)
                ->whereBetween('created_at', [$rangeStart, $rangeEnd])
                ->selectRaw('COALESCE(SUM(debit),0) as sum_debit, COALESCE(SUM(credit),0) as sum_credit')
                ->first();

            $sum_debit = $totals->sum_debit ?? 0;
            $sum_credit = $totals->sum_credit ?? 0;

            if ($this->tipe == 'D') {
                $this->startSaldo = $sum_debit - $sum_credit;
            } else {
                $this->startSaldo = $sum_credit - $sum_debit;
            }
            $this->runningSaldo = $this->startSaldo;
        }

        // compute month totals (for JUMLAH row)
        $monthTotals = Jurnal::where('coa_id', $this->coaId)
            ->whereYear('created_at', $this->year)
            ->whereMonth('created_at', $this->month)
            ->selectRaw('COALESCE(SUM(debit),0) as sum_debit, COALESCE(SUM(credit),0) as sum_credit')
            ->first();

        $this->sumDebitMonth = $monthTotals->sum_debit ?? 0;
        $this->sumCreditMonth = $monthTotals->sum_credit ?? 0;
    }

    public function query()
    {
        return Jurnal::with('order')
            ->where('coa_id', $this->coaId)
            ->whereYear('created_at', $this->year)
            ->whereMonth('created_at', $this->month)
            ->orderBy('created_at')
            ->orderBy('tipe')
            ->orderBy('nomor', 'asc');
    }

    public function map($row): array
    {
        if ($this->tipe == 'D') {
            if ($row->debit > 0) {
                $this->runningSaldo += $row->debit;
            } else {
                $this->runningSaldo -= $row->credit;
            }
        } else {
            if ($row->debit > 0) {
                $this->runningSaldo -= $row->debit;
            } else {
                $this->runningSaldo += $row->credit;
            }
        }

        $job = $row->order ? $row->order->job.'-'.sprintf('%02d',$row->order->no_job) : '-';

        return [
            $row->created_at->format('d/m/y'),
            $row->nomor,
            $row->container,
            $row->nopol,
            $job,
            $row->invoice,
            $row->nama,
            number_format($row->debit,2,',','.'),
            number_format($row->credit,2,',','.'),
            number_format($this->runningSaldo,2,',','.'),
            $row->no_bg
        ];
    }

    public function headings(): array
    {
        return [
            'Tanggal','Nomor','Container','Nopol','JOB','Invoice','Keterangan','Debit','Kredit','Saldo','No BG'
        ];
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // insert SALDO AWAL row above headings
                $sheet->insertNewRowBefore(1, 1);
                $sheet->setCellValue('A1', date('d/m/y', strtotime($this->last)));
                $sheet->setCellValue('B1', '-');
                $sheet->setCellValue('C1', '-');
                $sheet->setCellValue('D1', '-');
                $sheet->setCellValue('E1', '-');
                $sheet->setCellValue('F1', '-');
                $sheet->setCellValue('G1', 'SALDO AWAL');
                $sheet->setCellValue('H1', '-');
                $sheet->setCellValue('I1', '-');
                $sheet->setCellValue('J1', number_format($this->startSaldo,2,',','.'));
                $sheet->setCellValue('K1', '-');

                // write JUMLAH row after data
                $lastRow = $sheet->getHighestRow();
                $totRow = $lastRow + 1;
                $sheet->setCellValue('A'.$totRow, 'JUMLAH');
                $sheet->mergeCells("A{$totRow}:G{$totRow}");
                $sheet->setCellValue('H'.$totRow, number_format($this->sumDebitMonth,2,',','.'));
                $sheet->setCellValue('I'.$totRow, number_format($this->sumCreditMonth,2,',','.'));
                $sheet->setCellValue('J'.$totRow, number_format($this->runningSaldo,2,',','.'));
            }
        ];
    }
}
