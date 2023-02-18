<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $tipe = request('tipe') ?? 'all';
        if ($tipe != 'all') {
            $customers = Customer::all()->where('tipe',$tipe);
        }else{
            $customers = Customer::all();
        }
        $users = User::all();
        return view('admin.customer.index', compact('customers','users','tipe'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        Customer::create($data);
        return back()->with('success','Data berhasil disimpan!');
    }

    public function update(Customer $customer, Request $request)
    {
        $data = $request->all();
        $customer->update($data);
        return back()->with('success','Data berhasil dupdate!');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return back()->with('success','Data berhasil dihapus!');
    }
}
