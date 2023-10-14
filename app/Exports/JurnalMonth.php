<?php

namespace App\Exports;

use App\Models\Jurnal;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class JurnalMonth implements FromView
{
    private $month;
    private $year;

    public function __construct(int $year, int $month)
    {
        $this->month = $month;
        $this->year  = $year;
    }

    public function view(): View
    {
        $data = Jurnal::whereYear('created_at', $this->year)
                ->whereMonth('created_at', $this->month)
                ->get();

        return view('exports.jurnal_month', compact('data'));
    }
}
