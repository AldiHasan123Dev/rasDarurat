<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MultipleSheetExport implements WithMultipleSheets
{
    protected $start;
    protected $end;

    public function __construct($start, $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    public function sheets(): array
    {
        return [
            new XmlExport($this->start, $this->end),
            new Xml2Export($this->start, $this->end),
            // Tambahkan sheet lainnya jika diperlukan
        ];
    }
}

