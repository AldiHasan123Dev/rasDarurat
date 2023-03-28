<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\SubMenu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Menu::create([
            'title' => 'Master',
            'icon' => 'fas fa-database',
            'name' => 'master',
            'url' => '#',
        ]);
        Menu::create([
            'title' => 'Ekspedisi',
            'icon' => 'fas fa-train',
            'name' => 'ekspedisi',
            'url' => '#',
        ]);
        Menu::create([
            'title' => 'Keuangan',
            'icon' => 'fas fa-dollar',
            'name' => 'keuangan',
            'url' => '#',
        ]);
        Menu::create([
            'title' => 'Pajak',
            'icon' => 'fas fa-dollar',
            'name' => 'pajak',
            'url' => '#',
        ]);
        Menu::create([
            'title' => 'Trucking',
            'icon' => 'fas fa-truck',
            'name' => 'trucking',
            'url' => '#',
        ]);
        Menu::create([
            'title' => 'Laporan',
            'icon' => 'fas fa-list',
            'name' => 'trucking',
            'url' => '#',
        ]);

        $submenu = [
            ['menu_id'=>1,'title'=>'Customer','name'=>'customer','url'=>route('customer.index')],
            ['menu_id'=>1,'title'=>'Jadwal Kapal','name'=>'jadwal_kapal','url'=>route('jadwalkapal.index')],
            ['menu_id'=>1,'title'=>'Suplier','name'=>'suplier','url'=>route('pelayaran.index')],
            ['menu_id'=>1,'title'=>'User','name'=>'user','url'=>route('user.index')],
            ['menu_id'=>1,'title'=>'Role','name'=>'role','url'=>route('role.index')],
            ['menu_id'=>1,'title'=>'Data','name'=>'data','url'=>route('kapal.index')],
            ['menu_id'=>2,'title'=>'Surat Jalan','name'=>'surat_jalan','url'=>route('cetak.suratJalan')],
            ['menu_id'=>2,'title'=>'PO','name'=>'po','url'=>route('cetak.pickOrder')],
            ['menu_id'=>2,'title'=>'Order','name'=>'order','url'=>route('order.index')],
            ['menu_id'=>2,'title'=>'Shipping Instruction','name'=>'si','url'=>route('cetak.shipment')],
            ['menu_id'=>2,'title'=>'BA Kembali','name'=>'ba_kembali_ekspedisi','url'=>route('order.ba-kembali')],
            ['menu_id'=>2,'title'=>'Asuransi','name'=>'asuransi','url'=>route('order.asuransi')],
            ['menu_id'=>3,'title'=>'Order','name'=>'order_keuangan','url'=>route('keuangan.order')],
            ['menu_id'=>3,'title'=>'BA Kembali','name'=>'ba_kembali_keuangan','url'=>route('keuangan.ba_kembali')],
            ['menu_id'=>3,'title'=>'Pre Invoice','name'=>'pre_invoice','url'=>route('keuangan.pre_invoice')],
            ['menu_id'=>3,'title'=>'Invoice','name'=>'invoice','url'=>route('order.invoice')],
            ['menu_id'=>4,'title'=>'Master NPWP','name'=>'npwp','url'=>route('keuangan.customer',['filter'=>'keuangan'])],
            ['menu_id'=>4,'title'=>'Laporan PPN','name'=>'laporan_ppn','url'=>route('keuangan.laporan.ppn')],
            ['menu_id'=>4,'title'=>'Nomor Seri (NSFP)','name'=>'nsfp','url'=>route('nsfp.index')],
            ['menu_id'=>4,'title'=>'NSFP Ditarik','name'=>'nsfp_cancel','url'=>route('nsfp.cancel')],
            ['menu_id'=>5,'title'=>'Order Job','name'=>'trucking_order','url'=>route('trucking.order')],
            ['menu_id'=>5,'title'=>'Customer','name'=>'customer_trucking','url'=>route('customertrucking.index')],
            ['menu_id'=>5,'title'=>'Nopol','name'=>'nopol','url'=>route('kendaraan.index')],
            ['menu_id'=>5,'title'=>'Sopir','name'=>'sopir','url'=>route('sopir.index')],
            ['menu_id'=>5,'title'=>'Sangu Sopir','name'=>'sangu_sopir','url'=>route('sangusopir.index')],
            ['menu_id'=>5,'title'=>'Order Trucking','name'=>'order_trucking','url'=>route('ordertrucking.index')],
            ['menu_id'=>6,'title'=>'Pelayaran','name'=>'laporan_pelayaran','url'=>route('laporan.pelayaran')],
            ['menu_id'=>6,'title'=>'Tujuan','name'=>'laporan_tujuan','url'=>route('laporan.tujuan')],
            ['menu_id'=>6,'title'=>'Customer','name'=>'laporan_customer','url'=>route('laporan.customer')],
            ['menu_id'=>6,'title'=>'Marketing','name'=>'laporan_marketing','url'=>route('laporan.marketing')],
            ['menu_id'=>6,'title'=>'CS','name'=>'laporan_cs','url'=>route('laporan.cs')],
        ];

        SubMenu::insert($submenu);
    }
}
