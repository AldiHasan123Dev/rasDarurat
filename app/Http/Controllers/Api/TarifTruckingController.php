<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TarifTrucking;
use Illuminate\Http\Request;

class TarifTruckingController extends Controller
{
    public function createOrUpdate(Request $request)
    {
        $data = $request->all();
        $data['tarif'] = str_replace([',','.'],'',$request->tarif);

        try {
            if ($request->tarif_id) {
                TarifTrucking::find($request->tarif_id)->update($data);
            }else{
                TarifTrucking::create($data);
            }
            return response('success');
        } catch (\Throwable $th) {
            return response($th);
        }

    }
}
