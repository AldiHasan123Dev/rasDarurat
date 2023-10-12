<?php

namespace App\Exports;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class BAKembaliExport implements FromView
{
    public function view(): View
    {
        $order = Order::whereNull('ba_kembali')
        ->whereNull('invoice')
        ->whereHas('tarif', function($q){
            $q->whereIn('kondisi',[5,7]);
        })->get();
        $data = OrderResource::collection($order)->jsonSerialize();
        return view('exports.ba_kembali', compact('data'));
    }
}
