<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\JadwalKapalResource;
use App\Models\JadwalKapal;
use Illuminate\Http\Request;

class JadwalKapalController extends Controller
{
    public function getOne()
    {
        $data = JadwalKapal::find(request('id'));
        $res = new JadwalKapalResource($data);
        return response($res);
    }
}
