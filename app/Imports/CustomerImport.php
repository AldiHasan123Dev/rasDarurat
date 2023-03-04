<?php

namespace App\Imports;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class CustomerImport implements ToModel, WithStartRow
{

    public function model(array $row)
    {
        $check = Customer::where('nama', $row[0])->first();
        if ($row[11]!=''||$row[11]!=0||!empty($row[11])) {
            $marketing = User::where('name',$row[11])->first();
            if(!$marketing){
                $no = User::count() + 1;
                $marketing = User::create([
                    'name' => $row[11],
                    'email' => 'user'.$no.'@gmail.com',
                    'password' => Hash::make('password'),
                ]);
            }
            $marketing_id = $marketing->id;
        }else{
            $marketing_id = null;
        }

        if ($row[12]!=''||$row[12]!=0||!empty($row[12])) {
            $cs = User::where('name',$row[12])->first();
            if(!$cs){
                $no = User::count() + 1;
                $cs = User::create([
                    'name' => $row[12],
                    'email' => 'user'.$no.'@gmail.com',
                    'password' => Hash::make('password'),
                ]);
            }
            $cs_id = $cs->id;
        }else{
            $cs_id = null;
        }

        $data = [
            'nama' => $row[0],
            'alamat' => $row[1],
            'kota' => $row[2],
            'telp' => $row[3],
            'fax' => $row[4],
            'email' => $row[5],
            'hp' => $row[6],
            'nama_npwp' => $row[7],
            'alamat_npwp' => $row[8],
            'npwp' => $row[9],
            'nik' => $row[10],
            'marketing_id' => $marketing_id,
            'cs_id' => $cs_id,
        ];

        if(!$check){
            Customer::create($data);
        }else{
            $check->update($data);
        }
    }

    public function startRow(): int
    {
        return 2;
    }
}
