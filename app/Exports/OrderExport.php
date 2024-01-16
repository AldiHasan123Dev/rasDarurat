<?php

namespace App\Exports;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;

class OrderExport implements FromView
{
    private $from, $to;
    public function __construct($from,$to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    public function view(): View
    {
        $order = Order::whereBetween('created_at',[$this->from,$this->to])->get();
        $data = OrderResource::collection($order)->jsonSerialize();
        return view('exports.order', compact('data'));
    }
}
