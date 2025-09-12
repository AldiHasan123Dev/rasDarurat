<?php

namespace App\Exports;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class FortunaExport implements FromView
{
    private $text;
    public function __construct($text)
    {
        $this->text = $text;
    }

    public function view(): View
    {
        $orders = Order::where('job','LIKE',$this->text.'%')->whereHas('tarif', function($q){
            $q->where('customer_id',3532);
        })->orderBy('job')->get();
        $data = OrderResource::collection($orders);
        $data = $data->toArray(request());
        return view('exports.fortuna', compact('data'));
    }
}
