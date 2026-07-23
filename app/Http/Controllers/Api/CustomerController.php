<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function getOne()
    {
        $data = Customer::find(request('customer_id'));
        return response($data);
    }

      public function customerList(Request $request)
{
    $search = $request->q;

    $data = Customer::query()
        ->select('nama')
        ->when($search, function ($q) use ($search) {
            $q->where('nama', 'like', "%{$search}%");
        })
        ->distinct()
        ->orderBy('nama')
        ->limit(50)
        ->get();

    return response()->json([
        'results' => $data->map(function ($item) {
            return [
                'id'   => $item->nama,
                'text' => $item->nama,
            ];
        })
    ]);
}

public function marketingList(Request $request)
{
    $search = $request->q;

    $data = Customer::query()
        ->join('users', 'users.id', '=', 'customers.marketing_id')
        ->selectRaw('MIN(users.id) as id, users.name')
        ->when($search, function ($q) use ($search) {
            $q->where('users.name', 'like', "%{$search}%");
        })
        ->groupBy('users.name')
        ->orderBy('users.name')
        ->limit(50)
        ->get();

    return response()->json([
        'results' => $data->map(function ($item) {
            return [
                'id'   => $item->name,
                'text' => $item->name,
            ];
        })
    ]);
}

public function csList(Request $request)
{
    $search = $request->q;

    $data = Customer::query()
        ->join('users', 'users.id', '=', 'customers.cs_id')
        ->select('users.id', 'users.name')
        ->when($search, function ($q) use ($search) {
            $q->where('users.name', 'like', "%{$search}%");
        })
        ->distinct()
        ->orderBy('users.name')
        ->limit(50)
        ->get();

    return response()->json([
        'results' => $data->map(function ($item) {
            return [
                'id'   => $item->name,
                'text' => $item->name,
            ];
        })
    ]);
}

    public function getPengirim(Request $request)
{
    try {
        $isPaging = $request->has('page');

        // Debug untuk memastikan data terkirim
        // dd($request->all());

        $query = Customer::query();

        if ($request->has('cari') && $request->has('marketing')) {
            $query->where('nama', 'like', "%$request->cari%")
                  ->where('marketing_id', $request->marketing)
                  ->whereNotNull('npwp')
                  ->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(npwp, '.', ''), '-', ''), ' ', ''), '0', '') != ''");

            $counts = $query->count();
        } elseif ($request->has('cari')) {
            $query->where('nama', 'like', "%$request->cari%")
                  ->whereNotNull('npwp')
                  ->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(npwp, '.', ''), '-', ''), ' ', ''), '0', '') != ''");

            $counts = $query->count();
        } else {
            $counts = $query->count();
        }

        $items = $query->limit(20)
                       ->offset($isPaging ? ($request->page - 1) * 20 : 0)
                       ->get(['id', 'nama as text']);

        $res = [];
        foreach ($items as $idx => $it) {
            $res[$idx]['id'] = $it->id;
            $res[$idx]['text'] = $it->text . ' | ' . $it->id;
        }
    } catch (\Throwable $th) {
        return response([
            'message' => 'Gagal mendapatkan data pengirim',
            'system'  => $th->getMessage(),
        ], 500);
    }

    return response([
        'items'  => $res,
        'counts' => $counts,
    ], 200);
}


    public function getCustomer()
    {
        $name = request('nama');
        if(is_array($name)){
            $name = array_unique($name);
            $customer = Customer::whereIn('nama',$name)->get();
            if ($customer->count()!=count($name)) {
                return response(0);
            }
        }else{
            $customer = Customer::where('nama', $name)->first();
        }
        if (!$customer) {
            return response(0);
        }
        return response($customer);
    }

    public function update(Request $request)
    {
        $user = Customer::find($request->id);
        $user->update($request->all());

        return response('success');
    }

public function customerListAuthCs(Request $request)
{
    $search = $request->term;
    $ids = $request->ids ?? [];

    $customers = Customer::select('id','nama')
        ->when(!empty($ids), function ($q) use ($ids) {
            $q->whereIn('id', $ids);
        })
        ->when($search, function ($q) use ($search) {
            $q->where('nama', 'like', "%{$search}%");
        })
        ->orderBy('nama')
        ->get();

    return response()->json([
        'results' => $customers->map(function ($item) {
            return [
                'id' => $item->id,
                'text' => $item->nama,
            ];
        })
    ]);
}

}
