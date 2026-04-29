<?php

namespace App\Exports;

use App\Models\COA;
use App\Models\Jurnal;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class JurnalCoaExport implements WithTitle, FromView, ShouldAutoSize
{
    private int $month;
    private int $year;
    private int $coa;

    private $coaModel;

    public function __construct(int $coa, int $year, int $month)
    {
        $this->coa   = $coa;
        $this->month = $month;
        $this->year  = $year;

        $this->coaModel = COA::findOrFail($coa);
    }

    public function view(): View
    {
        $c = $this->coaModel;

        /*
        |--------------------------------------------------------------------------
        | QUERY DATA (OPTIMAL)
        |--------------------------------------------------------------------------
        */
        $query = Jurnal::query()
            ->with(['order:id,job,no_job']) // ✅ cegah N+1
            ->where('coa_id', $this->coa)
            ->whereYear('created_at', $this->year)
            ->whereMonth('created_at', $this->month)
            ->orderBy('created_at')
            ->orderBy('tipe')
            ->orderBy('nomor')
            ->select([
                'id',
                'created_at',
                'tipe',
                'nomor',
                'debit',
                'credit',
                'nama',
                'container',
                'nopol',
                'invoice',
                'no_bg',
                'order_id'
            ]);

        $data = $query->cursor(); // ✅ streaming

        /*
        |--------------------------------------------------------------------------
        | TIPE SALDO
        |--------------------------------------------------------------------------
        */
        $kode_awal = substr($c->kode, 0, 1);
        $tipe = in_array($kode_awal, ['2','3','5']) ? 'C' : 'D';

        /*
        |--------------------------------------------------------------------------
        | SALDO AWAL
        |--------------------------------------------------------------------------
        */
        $last = Carbon::create($this->year, $this->month)
            ->subMonth()
            ->endOfMonth();

        if (in_array($kode_awal, ['5','6','7'])) {

            $saldoAwal = 0;

        } else {

            $saldoData = Jurnal::query()
                ->where('coa_id', $this->coa)
                ->where('created_at', '<=', $last)
                ->selectRaw('SUM(debit) as debit, SUM(credit) as credit')
                ->first();

            $totalDebit  = $saldoData->debit ?? 0;
            $totalCredit = $saldoData->credit ?? 0;

            $saldoAwal = $tipe == 'D'
                ? $totalDebit - $totalCredit
                : $totalCredit - $totalDebit;
        }

        /*
        |--------------------------------------------------------------------------
        | SUMMARY (TIDAK PAKAI sum() DI BLADE)
        |--------------------------------------------------------------------------
        */
        $summary = Jurnal::query()
            ->where('coa_id', $this->coa)
            ->whereYear('created_at', $this->year)
            ->whereMonth('created_at', $this->month)
            ->selectRaw('SUM(debit) as debit, SUM(credit) as credit')
            ->first();

        /*
        |--------------------------------------------------------------------------
        | GENERATOR RUNNING SALDO (SUPER HEMAT MEMORY)
        |--------------------------------------------------------------------------
        */
        $data = $this->generateData($data, $tipe, $saldoAwal);

        return view('exports.jurnal', [
            'data'       => $data,
            'tipe'       => $tipe,
            'c'          => $c,
            'saldoAwal'  => $saldoAwal,
            'last'       => $last,
            'summary'    => $summary
        ]);
    }

    private function generateData($data, $tipe, $saldoAwal)
    {
        $saldo = $saldoAwal;

        foreach ($data as $item) {

            if ($tipe == 'D') {
                $saldo += $item->debit > 0 ? $item->debit : -$item->credit;
            } else {
                $saldo += $item->credit > 0 ? $item->credit : -$item->debit;
            }

            $item->running_saldo = $saldo;

            yield $item;
        }
    }

    public function title(): string
    {
        return $this->coaModel->kode . ' ' . $this->coaModel->nama;
    }
}