<?php

namespace App\Exports;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class MalindoExport implements FromView
{

    private $text;
    public function __construct($text)
    {
        $this->text = $text;
    }

    public function view(): View
    {
        $orders = Order::where('job','LIKE',$this->text.'%')->whereHas('tarif', function($q){
            $q->where('customer_id',256);
        })->get();
        $data = OrderResource::collection($orders);
        $data = $data->toArray(request());
        return view('exports.malindo', compact('data'));
    }
}
