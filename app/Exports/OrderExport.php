<?php

namespace App\Exports;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;

class OrderExport implements FromView
{
    public function view(): View
    {
        $order = Order::all();
        $data = OrderResource::collection($order)->jsonSerialize();
        return view('exports.order', compact('data'));
    }
}
