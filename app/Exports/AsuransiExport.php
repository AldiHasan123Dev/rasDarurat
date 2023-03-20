<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class AsuransiExport implements FromView
{
    private $ids;

    function __construct($ids= null)
    {
        $this->ids = json_decode($ids);
    }

    public function view(): View
    {
        return view('exports.asuransi', [
            'asuransi_job' => Order::whereIn('id',$this->ids)->where('order.asuransi','LIKE','%ADA%')->whereNotNull('asuransi_id')->where('tipe_asuransi','job')->get()->groupBy('job'),
            'asuransi_cont' => Order::whereIn('id',$this->ids)->where('order.asuransi','LIKE','%ADA%')->whereNotNull('asuransi_id')->where('tipe_asuransi','cont')->get(),
        ]);
    }
}
