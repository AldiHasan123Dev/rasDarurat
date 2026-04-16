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
    // 🔥 anti timeout + hemat memory
    ini_set('max_execution_time', 300);
    ini_set('memory_limit', '512M');
    \DB::connection()->disableQueryLog();

    $c = $this->coaModel;

    $start = Carbon::create($this->year, $this->month, 1)->startOfDay();
    $end   = Carbon::create($this->year, $this->month, 1)->endOfMonth();

    /*
    |--------------------------------------------------------------------------
    | 🔥 QUERY UTAMA (FIX N+1 + OPTIMAL)
    |--------------------------------------------------------------------------
    */
    $data = Jurnal::query()
        ->with(['order:id,job,no_job']) // 🔥 FIX N+1
        ->where('coa_id', $this->coa)
        ->whereBetween('created_at', [$start, $end])
        ->orderBy('created_at')
        ->orderBy('tipe')
        ->orderBy('nomor')
        ->select([
            'id',
            'created_at',
            'tipe',
            'nomor',
            'container',
            'nopol',
            'invoice',
            'no_bg',
            'nama',
            'debit',
            'credit',
            'order_id'
        ])
        ->cursor();

    /*
    |--------------------------------------------------------------------------
    | 🔥 TOTAL (JANGAN DI BLADE!)
    |--------------------------------------------------------------------------
    */
    $total = Jurnal::where('coa_id', $this->coa)
        ->whereBetween('created_at', [$start, $end])
        ->selectRaw('SUM(debit) as debit, SUM(credit) as credit')
        ->first();

    /*
    |--------------------------------------------------------------------------
    | 🔥 SALDO AWAL (OPTIMAL)
    |--------------------------------------------------------------------------
    */
    $kode_awal = substr($c->kode, 0, 1);
    $tipe = in_array($kode_awal, ['2','3','5']) ? 'C' : 'D';

    $last = $start->copy()->subDay();

    if (in_array($kode_awal, ['5','6','7'])) {
        $saldo = 0;
    } else {
        $saldoRaw = Jurnal::where('coa_id', $this->coa)
            ->where('created_at', '<=', $last)
            ->selectRaw('SUM(debit - credit) as saldo')
            ->value('saldo');

        $saldo = $tipe == 'D'
            ? ($saldoRaw ?? 0)
            : -($saldoRaw ?? 0);
    }

    return view('exports.jurnal', compact(
        'data',
        'tipe',
        'c',
        'saldo',
        'last',
        'total'
    ));
}

    public function title(): string
    {
        return $this->coaModel->kode . ' ' . $this->coaModel->nama;
    }
}