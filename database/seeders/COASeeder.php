<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\COA;
use App\Models\Neraca;
use App\Models\Subaccount;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class COASeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $neraca = [
            ['kode'=> '1', 'nama'=>'Aktiva'],
            ['kode'=> '2', 'nama'=>'Kewajiban'],
            ['kode'=> '3', 'nama'=>'Modal'],
        ];

        Neraca::insert($neraca);

        $accounts = [
            ['neraca_id'=>1, 'kode'=>'1.1', 'nama'=>'Aktiva Lancar'],
            ['neraca_id'=>1, 'kode'=>'1.2', 'nama'=>'Harta Tetap & Harta Tidak Berwujud'],
            ['neraca_id'=>1, 'kode'=>'1.3', 'nama'=>'Biaya Talangan'],
            ['neraca_id'=>1, 'kode'=>'1.4', 'nama'=>'Persediaan Segel'],
            ['neraca_id'=>1, 'kode'=>'1.5', 'nama'=>'Klaim Agent/Pelayaran'],
            ['neraca_id'=>1, 'kode'=>'1.6', 'nama'=>'Uang Muka'],
            ['neraca_id'=>1, 'kode'=>'1.7', 'nama'=>'Sewa Dibayar Dimuka'],
            ['neraca_id'=>1, 'kode'=>'1.8', 'nama'=>'Asuransi Dibayar Dimuka'],
            ['neraca_id'=>2, 'kode'=>'2.1', 'nama'=>'Kewajiban Lancar'],
            ['neraca_id'=>3, 'kode'=>'3.1', 'nama'=>'Modal'],
            ['neraca_id'=>3, 'kode'=>'3.2', 'nama'=>'Laba/Rugi Ditahan'],
            ['neraca_id'=>3, 'kode'=>'3.3', 'nama'=>'Laba/Rugi Tahun Berjalan'],
        ];

        Account::insert($accounts);

        $subaccount = [
            ['account_id'=>1, 'kode'=>'1.1.1', 'nama'=>'Kas'],
            ['account_id'=>1, 'kode'=>'1.1.2', 'nama'=>'Bank'],
            ['account_id'=>1, 'kode'=>'1.1.3', 'nama'=>'Piutang Usaha'],
            ['account_id'=>1, 'kode'=>'1.1.4', 'nama'=>'Piutang Lain-lain'],
            ['account_id'=>1, 'kode'=>'1.1.5', 'nama'=>'Piutang PPH'],
            ['account_id'=>1, 'kode'=>'1.1.6', 'nama'=>'PPN'],
            ['account_id'=>1, 'kode'=>'1.2.1', 'nama'=>'Harta Tetap'],
            ['account_id'=>1, 'kode'=>'1.3.1', 'nama'=>'Biaya Talangan'],
            ['account_id'=>1, 'kode'=>'1.3.2', 'nama'=>'Biaya Talangan (LSS) Hitachi'],
            ['account_id'=>1, 'kode'=>'1.3.3', 'nama'=>'Biaya Asuransi (Talangan)'],
            ['account_id'=>1, 'kode'=>'1.3.4', 'nama'=>'Jaminan Container'],
            ['account_id'=>1, 'kode'=>'1.3.5', 'nama'=>'Reimburst PPh 23 Meratus'],
            ['account_id'=>1, 'kode'=>'1.3.6', 'nama'=>'Biaya Talangan Ditagihkan Customer'],
            ['account_id'=>1, 'kode'=>'1.4.1', 'nama'=>'Persediaan Segel'],
            ['account_id'=>1, 'kode'=>'1.4.2', 'nama'=>'Persediaan Sparepart Trucking'],
            ['account_id'=>1, 'kode'=>'1.6.1', 'nama'=>'Uang Muka Biaya Oprasional Ekspedisi'],
            ['account_id'=>1, 'kode'=>'1.6.2', 'nama'=>'Uang Muka Biaya Oprasional Trucking'],
            ['account_id'=>1, 'kode'=>'1.6.3', 'nama'=>'Uang Muka Lain-lain'],
            ['account_id'=>1, 'kode'=>'1.7.1', 'nama'=>'Sewa Gudang KIG FB 16'],
            ['account_id'=>1, 'kode'=>'1.7.2', 'nama'=>'Sewa Gudang KIG FB 17'],
            ['account_id'=>1, 'kode'=>'1.7.3', 'nama'=>'Sewa Gudang KIG H3'],
            ['account_id'=>1, 'kode'=>'1.8.1', 'nama'=>'Asuransi Dibayar Dimuka Xpander L 1990 ABJ'],
            ['account_id'=>1, 'kode'=>'1.8.2', 'nama'=>'Asuransi Dibayar Dimuka Truck L 8145 UL'],
            ['account_id'=>1, 'kode'=>'1.8.3', 'nama'=>'Asuransi Dibayar Dimuka Truck L 8652 UM'],
            ['account_id'=>2, 'kode'=>'2.1.1', 'nama'=>'Hutang Usaha'],
            ['account_id'=>2, 'kode'=>'2.1.2', 'nama'=>'Hutang Lain-lain'],
            ['account_id'=>2, 'kode'=>'2.1.3', 'nama'=>'Hutang Pajak'],
            ['account_id'=>2, 'kode'=>'2.1.4', 'nama'=>'Uang Muka'],
            ['account_id'=>2, 'kode'=>'2.1.5', 'nama'=>'Hutang Biaya Operasional Ekspedisi & Trucking'],
        ];

        Subaccount::insert($subaccount);

        $coa = [
            ['id'=>1, 'coa_id'=>null ,'kode'=> '1', 'nama'=>'Aktiva'],
            ['id'=>2, 'coa_id'=>null ,'kode'=> '2', 'nama'=>'Kewajiban'],
            ['id'=>3, 'coa_id'=>null ,'kode'=> '3', 'nama'=>'Modal'],
            ['id'=>4 ,'coa_id'=>1, 'kode'=>'1.1', 'nama'=>'Aktiva Lancar'],
            ['id'=>5 ,'coa_id'=>1, 'kode'=>'1.2', 'nama'=>'Harta Tetap & Harta Tidak Berwujud'],
            ['id'=>6 ,'coa_id'=>1, 'kode'=>'1.3', 'nama'=>'Biaya Talangan'],
            ['id'=>7 ,'coa_id'=>1, 'kode'=>'1.4', 'nama'=>'Persediaan Segel'],
            ['id'=>8 ,'coa_id'=>1, 'kode'=>'1.5', 'nama'=>'Klaim Agent/Pelayaran'],
            ['id'=>9 ,'coa_id'=>1, 'kode'=>'1.6', 'nama'=>'Uang Muka'],
            ['id'=>10 ,'coa_id'=>1, 'kode'=>'1.7', 'nama'=>'Sewa Dibayar Dimuka'],
            ['id'=>11 ,'coa_id'=>1, 'kode'=>'1.8', 'nama'=>'Asuransi Dibayar Dimuka'],
            ['id'=>12 ,'coa_id'=>2, 'kode'=>'2.1', 'nama'=>'Kewajiban Lancar'],
            ['id'=>13 ,'coa_id'=>3, 'kode'=>'3.1', 'nama'=>'Modal'],
            ['id'=>14 ,'coa_id'=>3, 'kode'=>'3.2', 'nama'=>'Laba/Rugi Ditahan'],
            ['id'=>15 ,'coa_id'=>3, 'kode'=>'3.3', 'nama'=>'Laba/Rugi Tahun Berjalan'],
            ['id'=>16 ,'coa_id'=>4, 'kode'=>'1.1.1', 'nama'=>'Kas'],
            ['id'=>17 ,'coa_id'=>4, 'kode'=>'1.1.2', 'nama'=>'Bank'],
            ['id'=>18 ,'coa_id'=>4, 'kode'=>'1.1.3', 'nama'=>'Piutang Usaha'],
            ['id'=>19 ,'coa_id'=>4, 'kode'=>'1.1.4', 'nama'=>'Piutang Lain-lain'],
            ['id'=>20 ,'coa_id'=>4, 'kode'=>'1.1.5', 'nama'=>'Piutang PPH'],
            ['id'=>21 ,'coa_id'=>4, 'kode'=>'1.1.6', 'nama'=>'PPN'],
            ['id'=>22 ,'coa_id'=>5, 'kode'=>'1.2.1', 'nama'=>'Harta Tetap'],
            ['id'=>23 ,'coa_id'=>6, 'kode'=>'1.3.1', 'nama'=>'Biaya Talangan'],
            ['id'=>24 ,'coa_id'=>6, 'kode'=>'1.3.2', 'nama'=>'Biaya Talangan (LSS) Hitachi'],
            ['id'=>25 ,'coa_id'=>6, 'kode'=>'1.3.3', 'nama'=>'Biaya Asuransi (Talangan)'],
            ['id'=>26 ,'coa_id'=>6, 'kode'=>'1.3.4', 'nama'=>'Jaminan Container'],
            ['id'=>27 ,'coa_id'=>6, 'kode'=>'1.3.5', 'nama'=>'Reimburst PPh 23 Meratus'],
            ['id'=>28 ,'coa_id'=>6, 'kode'=>'1.3.6', 'nama'=>'Biaya Talangan Ditagihkan Customer'],
            ['id'=>29 ,'coa_id'=>7, 'kode'=>'1.4.1', 'nama'=>'Persediaan Segel'],
            ['id'=>30 ,'coa_id'=>7, 'kode'=>'1.4.2', 'nama'=>'Persediaan Sparepart Trucking'],
            ['id'=>31 ,'coa_id'=>9, 'kode'=>'1.6.1', 'nama'=>'Uang Muka Biaya Oprasional Ekspedisi'],
            ['id'=>32 ,'coa_id'=>9, 'kode'=>'1.6.2', 'nama'=>'Uang Muka Biaya Oprasional Trucking'],
            ['id'=>33 ,'coa_id'=>9, 'kode'=>'1.6.3', 'nama'=>'Uang Muka Lain-lain'],
            ['id'=>34 ,'coa_id'=>10, 'kode'=>'1.7.1', 'nama'=>'Sewa Gudang KIG FB 16'],
            ['id'=>35 ,'coa_id'=>10, 'kode'=>'1.7.2', 'nama'=>'Sewa Gudang KIG FB 17'],
            ['id'=>36 ,'coa_id'=>10, 'kode'=>'1.7.3', 'nama'=>'Sewa Gudang KIG H3'],
            ['id'=>37 ,'coa_id'=>11, 'kode'=>'1.8.1', 'nama'=>'Asuransi Dibayar Dimuka Xpander L 1990 ABJ'],
            ['id'=>38 ,'coa_id'=>11, 'kode'=>'1.8.2', 'nama'=>'Asuransi Dibayar Dimuka Truck L 8145 UL'],
            ['id'=>39 ,'coa_id'=>11, 'kode'=>'1.8.3', 'nama'=>'Asuransi Dibayar Dimuka Truck L 8652 UM'],
            ['id'=>40 ,'coa_id'=>12, 'kode'=>'2.1.1', 'nama'=>'Hutang Usaha'],
            ['id'=>41 ,'coa_id'=>12, 'kode'=>'2.1.2', 'nama'=>'Hutang Lain-lain'],
            ['id'=>42 ,'coa_id'=>12, 'kode'=>'2.1.3', 'nama'=>'Hutang Pajak'],
            ['id'=>43 ,'coa_id'=>12, 'kode'=>'2.1.4', 'nama'=>'Uang Muka'],
            ['id'=>44 ,'coa_id'=>12, 'kode'=>'2.1.5', 'nama'=>'Hutang Biaya Operasional Ekspedisi & Trucking'],
            ['id'=>45 ,'coa_id'=>17, 'kode'=>'1.1.2.1', 'nama'=>'Bank Mandiri 1400046005006'],
            ['id'=>46 ,'coa_id'=>18, 'kode'=>'1.1.3.1', 'nama'=>'Piutang Usaha Ekspedisi'],
            ['id'=>47 ,'coa_id'=>18, 'kode'=>'1.1.3.2', 'nama'=>'Piutang Usaha Trucking'],
            ['id'=>48 ,'coa_id'=>18, 'kode'=>'1.1.3.3', 'nama'=>'Piutang Usaha Sewa Gudang & Handling'],
            ['id'=>49 ,'coa_id'=>19, 'kode'=>'1.1.4.1', 'nama'=>'Piutang Sementara'],
            ['id'=>50 ,'coa_id'=>19, 'kode'=>'1.1.4.2', 'nama'=>'Piutang Karyawan'],
            ['id'=>51 ,'coa_id'=>20, 'kode'=>'1.1.5.1', 'nama'=>'Piutang PPh Pasal 23 (Bupot yang belum diterima)'],
            ['id'=>52 ,'coa_id'=>20, 'kode'=>'1.1.5.2', 'nama'=>'PPh pasal 23 dibayar di muka (Bupot Customer)'],
            ['id'=>53 ,'coa_id'=>20, 'kode'=>'1.1.5.3', 'nama'=>'PPh Pasal 4 ayat 2 (Final Sewa Tanah & Bangunan)'],
            ['id'=>54 ,'coa_id'=>20, 'kode'=>'1.1.5.4', 'nama'=>'Angsuran PPH pasal 25'],
            ['id'=>55 ,'coa_id'=>21, 'kode'=>'1.1.6.1', 'nama'=>'PPN Masukan'],
            ['id'=>56 ,'coa_id'=>21, 'kode'=>'1.1.6.2', 'nama'=>'PPN Keluaran'],
            ['id'=>57 ,'coa_id'=>22, 'kode'=>'1.2.1.1', 'nama'=>'Kendaraan/Vehicle'],
            ['id'=>58 ,'coa_id'=>22, 'kode'=>'1.2.1.2', 'nama'=>'Office & Equipment'],
            ['id'=>59 ,'coa_id'=>29, 'kode'=>'1.4.1.1', 'nama'=>'Persediaan Segel Meratus'],
            ['id'=>60 ,'coa_id'=>32, 'kode'=>'1.6.2.1', 'nama'=>'Uang Muka Biaya Operasional Trucking'],
            ['id'=>61 ,'coa_id'=>32, 'kode'=>'1.6.2.2', 'nama'=>'Uang Muka Biaya Operasional Trucking Ekspedisi'],
            ['id'=>62 ,'coa_id'=>40, 'kode'=>'2.1.1.1', 'nama'=>'Hutang Pelayaran'],
            ['id'=>63 ,'coa_id'=>40, 'kode'=>'2.1.1.1', 'nama'=>'Hutang Agent & Asuransi'],
            ['id'=>64 ,'coa_id'=>41, 'kode'=>'2.1.2.1', 'nama'=>'Pinjaman Lain-lain'],
            ['id'=>65 ,'coa_id'=>41, 'kode'=>'2.1.2.2', 'nama'=>'Pinjaman dari Bank Mandiri 400101581248'],
            ['id'=>66 ,'coa_id'=>41, 'kode'=>'2.1.2.3', 'nama'=>'Pinjaman dari OCBC'],
            ['id'=>67 ,'coa_id'=>42, 'kode'=>'2.1.3.1', 'nama'=>'Hutang PPN'],
            ['id'=>68 ,'coa_id'=>42, 'kode'=>'2.1.3.2', 'nama'=>'Hutang PPh pasal 4 Ayat 2'],
            ['id'=>69 ,'coa_id'=>42, 'kode'=>'2.1.3.3', 'nama'=>'Hutang PPh pasal 21'],
            ['id'=>70 ,'coa_id'=>42, 'kode'=>'2.1.3.4', 'nama'=>'Hutang PPh pasal 23'],
            ['id'=>71 ,'coa_id'=>42, 'kode'=>'2.1.3.5', 'nama'=>'Hutang PPh pasal 25'],
            ['id'=>72 ,'coa_id'=>42, 'kode'=>'2.1.3.6', 'nama'=>'Hutang PPh pasal 29'],
            ['id'=>73 ,'coa_id'=>42, 'kode'=>'2.1.3.7', 'nama'=>'Hutang PPh 23 Vendor Potongan PPh 23 Vendor'],
            ['id'=>74 ,'coa_id'=>43, 'kode'=>'2.1.4.1', 'nama'=>'Uang Muka dari Customer Ekspedisi'],
            ['id'=>75 ,'coa_id'=>43, 'kode'=>'2.1.4.2', 'nama'=>'Uang Muka dari Customer Trucking'],
            ['id'=>76 ,'coa_id'=>44, 'kode'=>'2.1.5.1', 'nama'=>'Hutang Biaya Oprasional Ekspedisi'],
            ['id'=>77 ,'coa_id'=>44, 'kode'=>'2.1.5.2', 'nama'=>'Hutang Biaya Oprasional Trucking'],
            ['id'=>78 ,'coa_id'=>57, 'kode'=>'1.2.1.1.1', 'nama'=>'Akumulasi Penyusutan Kendaraan/ Vehicle'],
            ['id'=>79 ,'coa_id'=>58, 'kode'=>'1.2.1.2.1', 'nama'=>'Akumulasi Penyusutan Office & Equipment'],
            ['id'=>80 ,'coa_id'=>77, 'kode'=>'2.1.5.2.1', 'nama'=>'Hutang Biaya Oprasional Trucking'],
            ['id'=>81 ,'coa_id'=>77, 'kode'=>'2.1.5.2.2', 'nama'=>'Hutang Biaya Oprasional Trucking Ekspedisi'],
        ];

        COA::insert($coa);
    }
}
