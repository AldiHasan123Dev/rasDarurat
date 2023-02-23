<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TarifResource;
use App\Models\Tarif;
use Illuminate\Http\Request;

class TarifController extends Controller
{
    public function getOne()
    {
        $data = Tarif::find(request('id'));
        $res = new TarifResource($data);
        return response($res);
    }
}
