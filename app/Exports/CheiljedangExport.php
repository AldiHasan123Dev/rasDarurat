<?php

namespace App\Exports;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CheiljedangExport implements FromView, ShouldAutoSize
{

    private $text;
    public function __construct($text)
    {
        $this->text = $text;
    }

    public function view(): View
    {
        $orders = Order::where('job', 'LIKE', $this->text . '%')->whereHas('tarif', function ($q) {
            $q->whereHas('customer', function ($qu) {
                $qu->where('nama', 'like', '%PT. CJ. CHEILJEDANG FEED KALIMANTAN%');
            });
        })->get();
        $data = OrderResource::collection($orders);
        $data = $data->toArray(request());
        return view('exports.cheiljedang', compact('data'));
    }
}
