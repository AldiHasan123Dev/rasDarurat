<?php

namespace App\Exports;

use App\Http\Resources\OrderTruckingResource;
use App\Models\OrderTrucking;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;

class OrderTruckingExport implements FromView
{
    public function view(): View
    {
        $order = OrderTrucking::all();
        $data = OrderTruckingResource::collection($order)->jsonSerialize();
        return view('exports.order_trucking', compact('data'));
    }
}
