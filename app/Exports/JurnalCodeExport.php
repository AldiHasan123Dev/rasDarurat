<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;


class JurnalCodeExport implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = collect($data);
    }

    public function collection(): Collection
    {
        return $this->data->map(function ($jurnal) {
            return [
                'ID'                => $jurnal->id,
                'Tanggal' => \Carbon\Carbon::parse($jurnal->created_at)->format('d-m-Y'),
                'COA'               => $jurnal->coa->nama ?? '',
                'Nomor'             => $jurnal->nomor,
                'Debit'             => $jurnal->debit ?? 0,
                'Kredit'            => $jurnal->credit ?? 0,
                'Kode'              => $jurnal->kode ?? '',
                'Invoice External'  => $jurnal->invoice_external ?? '',
                'Invoice Agen'      => $jurnal->invoice_agen ?? '',
                'Invoice Vendor'    => $jurnal->invoice_vendor ?? '',
                'Invoice Trucking'  => $jurnal->invoice_trucking ?? '',
                'Container'         => $jurnal->container ?? '',
                'Keterangan'        => $jurnal->keterangan ?? '',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Tanggal',
            'COA',
            'Nomor',
            'Debit',
            'Kredit',
            'Kode',
            'Invoice External',
            'Invoice Agen',
            'Invoice Vendor',
            'Invoice Trucking',
            'Container',
            'Keterangan'
        ];
    }
}
