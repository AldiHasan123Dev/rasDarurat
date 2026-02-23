<?php

namespace App\Exports;

use App\Models\COA;
use App\Models\Jurnal;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
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

    $data = Jurnal::query()
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
            'nama'
        ])
        ->cursor(); // 🔥 STREAMING SUPER HEMAT MEMORY


    /*
    |--------------------------------------------------------------------------
    | SALDO AWAL (OPTIMASI)
    |--------------------------------------------------------------------------
    */

    $kode_awal = substr($c->kode, 0, 1);

    $tipe = in_array($kode_awal, ['2','3','5']) ? 'C' : 'D';


    $last = Carbon::create($this->year, $this->month)
        ->subMonth()
        ->endOfMonth();


    if (in_array($kode_awal, ['5','6','7'])) {

        $saldo = 0;

    } else {

        $saldoData = Jurnal::query()

            ->where('coa_id', $this->coa)

            ->where('created_at', '<=', $last)

            ->selectRaw('
                SUM(debit) as debit,
                SUM(credit) as credit
            ')
            ->first();


        $totalDebit  = $saldoData->debit ?? 0;

        $totalCredit = $saldoData->credit ?? 0;


        $saldo = $tipe == 'D'

            ? $totalDebit - $totalCredit

            : $totalCredit - $totalDebit;
    }


    return view('exports.jurnal', compact(
        'data',
        'tipe',
        'c',
        'saldo',
        'last'
    ));
}


    public function title(): string
    {
        return $this->coaModel->kode.' '.$this->coaModel->nama;
    }

}