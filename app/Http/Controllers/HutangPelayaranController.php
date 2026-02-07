<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use App\Models\Pelayaran;
use Illuminate\Http\Request;
use App\Models\TarifPelayaran;
use App\Models\HutangPelayaran;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use App\Models\COA;
use App\Models\JadwalKapal;
use App\Models\Jurnal;
use App\Models\Setting;
use App\Models\TemplateJurnal;
use App\Models\TemplateJurnalItem;
use Illuminate\Support\Facades\Hash;

class HutangPelayaranController extends Controller
{
    protected $sno;
    public function __construct()
    {
        $setting = Setting::find(1);
        $this->sno = $setting->short_name;
    }

    public function index()
    {
        $lists = HutangPelayaran::where('status',0)->pluck('order_id')->toArray();
        $kapal = [];
        $pelayaran = [];
        if(request('kapal')){
            $kapal = request('kapal');
        }
        if(request('pelayaran')){
            $pelayaran = request('pelayaran');
        }
        $q = Order::query();
            $q->join('jadwal_kapal','jadwal_kapal.id','=','order.jadwal_kapal_id');
            $q->join('pelayaran','pelayaran.id','=','jadwal_kapal.pelayaran_id');
            $q->join('kapal','kapal.id','=','jadwal_kapal.kapal_id');
            $q->join('tarif','tarif.id','=','order.tarif_id');
            $q->join('shipments','tarif.shipment','=','shipments.id');
            $q->join('lokasi as dari','dari.id','=','tarif.dari');
            $q->join('lokasi as tujuan','tujuan.id','=','tarif.tujuan');
            $q->join('hutang_pelayaran','hutang_pelayaran.order_id','=','order.id');
            $q->whereIn('order.id',$lists);
            $q->where('hutang_pelayaran.status',0);
            if(request('pelayaran')){
                $q->whereIn('jadwal_kapal.pelayaran_id',$pelayaran);
            }
            if(request('kapal')){
                $q->whereIn('kapal.nama',$kapal);
            }
            $q->select('order.job','order.port_id','order.tipe','hutang_pelayaran.is_lock','hutang_pelayaran.ut','dari.nama as dari','tujuan.nama as tujuan','order.tarif_id','order.container','order.seal','order.no_job','order.id','order.jadwal_kapal_id','jadwal_kapal.pelayaran_id','jadwal_kapal.kapal_id','jadwal_kapal.voyage','kapal.nama as nama_kapal','pelayaran.nama','shipments.nama as fit');
            $q->orderBy('order.job')->orderBy('order.no_job');
            $data = $q->get()->groupBy('jadwal_kapal.pelayaran_id','jadwal_kapal.kapal_id');
        return view('admin.hutangpelayaran.index', compact('data','pelayaran','kapal'));
    }

    public function cetak()
    {
        $data = HutangPelayaran::where('status',1)->whereNotNull('invoice')->orderBy('invoice','desc')->get()->groupBy('invoice');
        return view('admin.hutangpelayaran.cetak', compact('data'));
    }

public function store(Request $request)
{
    $data = $request->all();
    $ids = [];
    $n = HutangPelayaran::max('no') + 1;
    $code = 'HP/' . date('ymd') . '/' . sprintf('%02d', $n);

    // Ambil ID COA
    $c31 = COA::where('coa_ras', 31)->value('id') ?? 31;
    $c28 = COA::where('coa_ras', 24)->value('id') ?? 24;
    $c73 = COA::where('coa_ras', 73)->value('id') ?? 73;
    $c130 = COA::where('coa_ras', 130)->value('id') ?? 130;
    $c62 = COA::where('coa_ras', 62)->value('id') ?? 62;
    $c23 = COA::where('coa_ras', 23)->value('id') ?? 23;

    // Update hutang pelayaran (masih di luar transaction)
    foreach ($data['data'] as $id => $item) {
        $prop = $item;
        $prop['no'] = $n;
        $prop['invoice'] = $code;
        $prop['tgl_invoice'] = date('Y-m-d');
        $prop['tgl_bg_opp'] = $data['tanggal_bg_opp'] ?? null;
        $prop['tgl_bg_opt'] = $data['tanggal_bg_opt'] ?? null;
        $prop['tgl_bg_ut'] = $data['tanggal_bg_ut'] ?? null;
        $prop['no_bg_opp'] = $data['no_bg_opp'] ?? null;
        $prop['no_bg_opt'] = $data['no_bg_opt'] ?? null;
        $prop['no_bg_ut'] = $data['no_bg_ut'] ?? null;
        $prop['nominal_bg_opp'] = $data['nominal_bg_opp'] ?? 0;
        $prop['nominal_bg_opt'] = $data['nominal_bg_opt'] ?? 0;
        $prop['nominal_bg_ut'] = $data['nominal_bg_ut'] ?? 0;
        $prop['pph'] = $data['pph'] ?? 0;
        $prop['opt_pph'] = $data['opt_pph'] ?? 0;
        $prop['pembulatan'] = $data['pembulatan'] ?? 0;
        $prop['penambahan'] = $data['penambahan'] ?? null;
        $prop['penambahan_nominal'] = $data['penambahan_nominal'] ?? 0;
        $prop['status'] = 1;

        $hp = HutangPelayaran::where('order_id', $id)->first();
        $hp->update($prop);
        $ids[] = $hp->id;
    }

    // Siapkan nomor jurnal (atomic dan aman)
    try {
        DB::transaction(function () use ($data, &$ids, $c31, $c28, $c73, $c130, $c62, $c23, $code) {
            // Ambil semua hutang yang diupdate
            $lists = HutangPelayaran::with([
                'order',
                'order.tarif',
                'order.tarif.shipmentInfo',
                'order.tarif.customer',
                'order.jadwal_kapal.kapal'
            ])->whereIn('id', $ids)->get();

            // Ambil daftar no_bg dari request (unique, non-null)
            $tgl = [$data['no_bg_opp'] ?? null, $data['no_bg_opt'] ?? null, $data['no_bg_ut'] ?? null];
            $tgl_group = array_values(array_filter(array_unique($tgl)));

            // Jika sudah ada jurnal untuk salah satu no_bg, batalkan (keamanan & konsistensi)
            // if (!empty($tgl_group)) {
            //     $exists = Jurnal::where('tipe', 'JNL')->whereIn('no_bg', $tgl_group)->exists();
            //     if ($exists) {
            //         // lempar exception agar transaction rollback
            //         throw new \Exception('Gagal, hutang pelayaran sudah dijurnal (salah satu BG sudah ada pada jurnal).');
            //     }
            // }

            // Ambil last no + lock untuk menghindari duplikasi nomor jika paralel
            $lastNo = Jurnal::where('tipe', 'JNL')
                ->whereMonth('created_at', date('m'))
                ->whereYear('created_at', date('Y'))
                ->orderByDesc('no')
                ->lockForUpdate()
                ->value('no');

            $no = ($lastNo ? $lastNo + 1 : 1);

            // Buat mapping nomor per no_bg (key sebagai string)
            $data_nomor = [];
            foreach ($tgl_group as $tg) {
                $key = (string)$tg;
                $nomor = sprintf('%02d', date('m')) . '-' . sprintf('%03d', $no) . '/' . ($this->sno == 'ALB' ? 'ALB/' : '') . date('y');
                $data_nomor[$key] = ['no' => $no, 'nomor' => $nomor];
                $no++;
            }

            $jurnalData = [];
            $opp_total = 0;
            $opt_total = 0;
            $ut_total = 0;
            $now = now();

            foreach ($lists as $item) {
                // relasi asli memakai snake_case (sesuaikan dengan project)
                $shipQty = preg_replace("/[^0-9]/", "", $item->order->tarif->shipmentInfo->nama ?? '');

                $oppFields = ['opp', 'apbs', 'cleaning', 'thc', 'lss', 'opp_stamp', 'hp_seal','vgm'];
                $optFields = ['opt', 'opt_stamp'];
                $utFields = ['ut', 'ut_stamp', 'bl', 'ut_cleaning'];

                // OPP
                foreach ($oppFields as $a) {
                    $coa_id = $c31;
                    if ($a === 'thc') $title = 'THC LOLO';
                    elseif ($a === 'opp_stamp') $title = 'STAMP OPP';
                    elseif ($a === 'hp_seal') $title = 'Pembelian SEAL';
                    elseif ($a === 'vgm') $title = 'Biaya VGM Terminal';
                    else $title = strtoupper($a);

                    if ($a === 'lss') {
                        $customer_id = (int)($item->order->tarif->customer_id ?? 0);
                        if ($customer_id === 318 || $customer_id === 3134) $coa_id = $c28;
                    }

                    $name = $title . ' ' .
                        ($item->order->jadwal_kapal->kapal->nama ?? '') . ' V. ' . ($item->order->jadwal_kapal->voyage ?? '') .
                        ' (1X' . $shipQty . ' )  ' . ($item->order->tarif->customer->nama ?? '') .
                        ' ( ' . ($item->order->job ?? '') . '-' . sprintf('%02d', ($item->order->no_job ?? 0)) . ')';

                    if (!empty($item->{$a}) && !is_null($item->no_bg_opp)) {
                        $key = (string)$item->no_bg_opp;
                        if (!isset($data_nomor[$key])) {
                            // safety fallback (seharusnya sudah ada)
                            throw new \Exception("Nomor jurnal untuk BG OPP {$item->no_bg_opp} belum disiapkan.");
                        }
                        $jurnalData[] = [
                            'tipe' => 'JNL',
                            'no_bg' => $item->no_bg_opp,
                            'tgl_bg' => $item->tgl_bg_opp,
                            'nominal_bg' => $item->nominal_bg_opp,
                            'coa_id' => $coa_id,
                            'order_id' => $item->order_id,
                            'nomor' => $data_nomor[$key]['nomor'],
                            'relasi' => $data_nomor[$key]['nomor'],
                            'no' => $data_nomor[$key]['no'],
                            'nama' => $name,
                            'debit' => $item->{$a},
                            'credit' => 0,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                        $opp_total += $item->{$a};
                    }
                }

                // OPT
                foreach ($optFields as $a) {
                    $title = $a == 'opt_stamp' ? 'STAMP OPT' : strtoupper($a);
                    $name = $title . ' ' .
                        ($item->order->jadwal_kapal->kapal->nama ?? '') . ' V. ' . ($item->order->jadwal_kapal->voyage ?? '') .
                        ' (1X' . $shipQty . ' )  ' . ($item->order->tarif->customer->nama ?? '') .
                        ' ( ' . ($item->order->job ?? '') . '-' . sprintf('%02d', ($item->order->no_job ?? 0)) . ')';

                    if (!empty($item->{$a}) && !is_null($item->no_bg_opt)) {
                        $key = (string)$item->no_bg_opt;
                        if (!isset($data_nomor[$key])) {
                            throw new \Exception("Nomor jurnal untuk BG OPT {$item->no_bg_opt} belum disiapkan.");
                        }
                        $jurnalData[] = [
                            'tipe' => 'JNL',
                            'no_bg' => $item->no_bg_opt,
                            'tgl_bg' => $item->tgl_bg_opt,
                            'nominal_bg' => $item->nominal_bg_opt,
                            'coa_id' => $c31,
                            'order_id' => $item->order_id,
                            'nomor' => $data_nomor[$key]['nomor'],
                            'relasi' => $data_nomor[$key]['nomor'],
                            'no' => $data_nomor[$key]['no'],
                            'nama' => $name,
                            'debit' => $item->{$a},
                            'credit' => 0,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                        $opt_total += $item->{$a};
                    }
                }

                // UT
                foreach ($utFields as $a) {
                    if ($a == 'ut_stamp') $title = 'STAMP UT';
                    elseif ($a == 'ut_cleaning') $title = 'CLEANING';
                    else $title = strtoupper($a);

                    $name = $title . ' ' .
                        ($item->order->jadwal_kapal->kapal->nama ?? '') . ' V. ' . ($item->order->jadwal_kapal->voyage ?? '') .
                        ' (1X' . $shipQty . ' )  ' . ($item->order->tarif->customer->nama ?? '') .
                        ' ( ' . ($item->order->job ?? '') . '-' . sprintf('%02d', ($item->order->no_job ?? 0)) . ')';

                    if (!empty($item->{$a}) && !is_null($item->no_bg_ut)) {
                        $key = (string)$item->no_bg_ut;
                        if (!isset($data_nomor[$key])) {
                            throw new \Exception("Nomor jurnal untuk BG UT {$item->no_bg_ut} belum disiapkan.");
                        }
                        $jurnalData[] = [
                            'tipe' => 'JNL',
                            'no_bg' => $item->no_bg_ut,
                            'tgl_bg' => $item->tgl_bg_ut,
                            'nominal_bg' => $item->nominal_bg_ut,
                            'coa_id' => $c31,
                            'order_id' => $item->order_id,
                            'nomor' => $data_nomor[$key]['nomor'],
                            'relasi' => $data_nomor[$key]['nomor'],
                            'no' => $data_nomor[$key]['no'],
                            'nama' => $name,
                            'debit' => $item->{$a},
                            'credit' => 0,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                        $ut_total += $item->{$a};
                    }
                }
            } // end foreach lists

            // Tambah jurnal tambahan: PPH / pembulatan / hutang total (sama logika seperti kode lama)
            // Ambil salah satu hp untuk referensi nilai pph/pembulatan/penambahan
            $hp_ref = HutangPelayaran::whereIn('id', $ids)->first();

            if ($hp_ref) {
                // PPH (potongan)
                if ($hp_ref->pph > 0 && !is_null($hp_ref->no_bg_opp)) {
                    $key = (string)$hp_ref->no_bg_opp;
                    if (!isset($data_nomor[$key])) {
                        throw new \Exception("Nomor jurnal untuk BG OPP {$hp_ref->no_bg_opp} belum disiapkan (PPH).");
                    }
                    $jurnalData[] = [
                        'tipe' => 'JNL',
                        'no_bg' => $hp_ref->no_bg_opp,
                        'tgl_bg' => $hp_ref->tgl_bg_opp,
                        'nominal_bg' => $hp_ref->nominal_bg_opp,
                        'coa_id' => $c73,
                        'order_id' => null,
                        'nomor' => $data_nomor[$key]['nomor'],
                        'relasi' => $data_nomor[$key]['nomor'],
                        'no' => $data_nomor[$key]['no'],
                        'nama' => 'Potongan PPH 23 ' . ($hp_ref->order->jadwal_kapal->kapal->nama ?? '') . ' V. ' . ($hp_ref->order->jadwal_kapal->voyage ?? ''),
                        'debit' => 0,
                        'credit' => $hp_ref->pph,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
                 if ($hp_ref->opt_pph > 0 && !is_null($hp_ref->no_bg_opt)) {
                    $key = (string)$hp_ref->no_bg_opt;
                    if (!isset($data_nomor[$key])) {
                        throw new \Exception("Nomor jurnal untuk BG OPT {$hp_ref->no_bg_opt} belum disiapkan (PPH).");
                    }
                    $jurnalData[] = [
                        'tipe' => 'JNL',
                        'no_bg' => $hp_ref->no_bg_opt,
                        'tgl_bg' => $hp_ref->tgl_bg_opt,
                        'nominal_bg' => $hp_ref->nominal_bg_opt,
                        'coa_id' => $c73,
                        'order_id' => null,
                        'nomor' => $data_nomor[$key]['nomor'],
                        'relasi' => $data_nomor[$key]['nomor'],
                        'no' => $data_nomor[$key]['no'],
                        'nama' => 'Potongan PPH 23 ' . ($hp_ref->order->jadwal_kapal->kapal->nama ?? '') . ' V. ' . ($hp_ref->order->jadwal_kapal->voyage ?? ''),
                        'debit' => 0,
                        'credit' => $hp_ref->opt_pph,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                // Pembulatan (ikut ke OPP)
                if ($hp_ref->pembulatan != 0 && !is_null($hp_ref->no_bg_opp)) {
                    $key = (string)$hp_ref->no_bg_opp;
                    if (!isset($data_nomor[$key])) {
                        throw new \Exception("Nomor jurnal untuk BG OPP {$hp_ref->no_bg_opp} belum disiapkan (Pembulatan).");
                    }
                    $jurnalData[] = [
                        'tipe' => 'JNL',
                        'no_bg' => $hp_ref->no_bg_opp,
                        'tgl_bg' => $hp_ref->tgl_bg_opp,
                        'nominal_bg' => $hp_ref->nominal_bg_opp,
                        'coa_id' => $c130,
                        'order_id' => null,
                        'nomor' => $data_nomor[$key]['nomor'],
                        'relasi' => $data_nomor[$key]['nomor'],
                        'no' => $data_nomor[$key]['no'],
                        'nama' => 'Pembulatan OPP ' . ($hp_ref->order->jadwal_kapal->kapal->nama ?? '') . ' V. ' . ($hp_ref->order->jadwal_kapal->voyage ?? ''),
                        'debit' => $hp_ref->pembulatan,
                        'credit' => 0,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                    $opp_total += $hp_ref->pembulatan;
                }

                // Hutang OPP (credit = opp_total - pph)
                if (!is_null($hp_ref->no_bg_opp)) {
                    $key = (string)$hp_ref->no_bg_opp;
                    if (!isset($data_nomor[$key])) {
                        throw new \Exception("Nomor jurnal untuk BG OPP {$hp_ref->no_bg_opp} belum disiapkan (Hutang OPP).");
                    }
                    $jurnalData[] = [
                        'tipe' => 'JNL',
                        'no_bg' => $hp_ref->no_bg_opp,
                        'tgl_bg' => $hp_ref->tgl_bg_opp,
                        'nominal_bg' => $hp_ref->nominal_bg_opp,
                        'coa_id' => $c62,
                        'order_id' => null,
                        'nomor' => $data_nomor[$key]['nomor'],
                        'relasi' => $data_nomor[$key]['nomor'],
                        'no' => $data_nomor[$key]['no'],
                        'nama' => 'Hutang OPP ' . ($hp_ref->order->jadwal_kapal->pelayaran->nama ?? '') . ' : ' . ($hp_ref->order->jadwal_kapal->kapal->nama ?? '') . ' V. ' . ($hp_ref->order->jadwal_kapal->voyage ?? '') . ' BG: ' . $hp_ref->no_bg_opp . ' (' . date('d/m/y', strtotime($hp_ref->tgl_bg_opp)) . ')',
                        'debit' => 0,
                        'credit' => max(0, $opp_total - ($hp_ref->pph ?? 0)),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                // Hutang OPT
                if (!is_null($hp_ref->no_bg_opt)) {
                    $key = (string)$hp_ref->no_bg_opt;
                    if (!isset($data_nomor[$key])) {
                        throw new \Exception("Nomor jurnal untuk BG OPT {$hp_ref->no_bg_opt} belum disiapkan (Hutang OPT).");
                    }
                    $jurnalData[] = [
                        'tipe' => 'JNL',
                        'no_bg' => $hp_ref->no_bg_opt,
                        'tgl_bg' => $hp_ref->tgl_bg_opt,
                        'nominal_bg' => $hp_ref->nominal_bg_opt,
                        'coa_id' => $c62,
                        'order_id' => null,
                        'nomor' => $data_nomor[$key]['nomor'],
                        'relasi' => $data_nomor[$key]['nomor'],
                        'no' => $data_nomor[$key]['no'],
                        'nama' => 'Hutang OPT ' . ($hp_ref->order->jadwal_kapal->pelayaran->nama ?? '') . ' : ' . ($hp_ref->order->jadwal_kapal->kapal->nama ?? '') . ' V. ' . ($hp_ref->order->jadwal_kapal->voyage ?? '') . ' BG: ' . $hp_ref->no_bg_opt . ' (' . date('d/m/y', strtotime($hp_ref->tgl_bg_opt)) . ')',
                        'debit' => 0,
                        'credit' => $opt_total,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                // Hutang UT + penambahan_nominal
                if (!is_null($hp_ref->no_bg_ut)) {
                    $key = (string)$hp_ref->no_bg_ut;
                    if (!isset($data_nomor[$key])) {
                        throw new \Exception("Nomor jurnal untuk BG UT {$hp_ref->no_bg_ut} belum disiapkan (Hutang UT).");
                    }
                    $jurnalData[] = [
                        'tipe' => 'JNL',
                        'no_bg' => $hp_ref->no_bg_ut,
                        'tgl_bg' => $hp_ref->tgl_bg_ut,
                        'nominal_bg' => $hp_ref->nominal_bg_ut,
                        'coa_id' => $c62,
                        'order_id' => null,
                        'nomor' => $data_nomor[$key]['nomor'],
                        'relasi' => $data_nomor[$key]['nomor'],
                        'no' => $data_nomor[$key]['no'],
                        'nama' => 'Hutang UT ' . ($hp_ref->order->jadwal_kapal->pelayaran->nama ?? '') . ' : ' . ($hp_ref->order->jadwal_kapal->kapal->nama ?? '') . ' V. ' . ($hp_ref->order->jadwal_kapal->voyage ?? '') . ' BG: ' . $hp_ref->no_bg_ut . ' (' . date('d/m/y', strtotime($hp_ref->tgl_bg_ut)) . ')',
                        'debit' => 0,
                        'credit' => $ut_total + ($hp_ref->penambahan_nominal ?? 0),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    // Penambahan (jika ada)
                    if (!empty($hp_ref->penambahan) && ($hp_ref->penambahan_nominal ?? 0) != 0) {
                        $coa_for_penambahan = (stripos($hp_ref->penambahan, 'pph 23') !== false) ? $c73 : $c23;
                        if ($hp_ref->penambahan_nominal > 0) {
                            $jurnalData[] = [
                                'tipe' => 'JNL',
                                'no_bg' => $hp_ref->no_bg_ut,
                                'tgl_bg' => $hp_ref->tgl_bg_ut,
                                'nominal_bg' => $hp_ref->nominal_bg_ut,
                                'coa_id' => $coa_for_penambahan,
                                'order_id' => null,
                                'nomor' => $data_nomor[$key]['nomor'],
                                'relasi' => $data_nomor[$key]['nomor'],
                                'no' => $data_nomor[$key]['no'],
                                'nama' => $hp_ref->penambahan,
                                'debit' => $hp_ref->penambahan_nominal,
                                'credit' => 0,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ];
                        } else {
                            $jurnalData[] = [
                                'tipe' => 'JNL',
                                'no_bg' => $hp_ref->no_bg_ut,
                                'tgl_bg' => $hp_ref->tgl_bg_ut,
                                'nominal_bg' => $hp_ref->nominal_bg_ut,
                                'coa_id' => $coa_for_penambahan,
                                'order_id' => null,
                                'nomor' => $data_nomor[$key]['nomor'],
                                'relasi' => $data_nomor[$key]['nomor'],
                                'no' => $data_nomor[$key]['no'],
                                'nama' => $hp_ref->penambahan,
                                'debit' => 0,
                                'credit' => $hp_ref->penambahan_nominal * -1,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ];
                        }
                    }
                }
            }

            // Insert bulk semua jurnal
            if (!empty($jurnalData)) {
                Jurnal::insert($jurnalData);
            }

            // Update field jurnal_opp/opt/ut pada tiap HutangPelayaran (mirip kode lama)
            $dataHp = HutangPelayaran::whereIn('id', $ids)->get();
            foreach ($dataHp as $hpItem) {
                $opp = Jurnal::where('no_bg', $hpItem->no_bg_opp)->where('tipe', 'JNL')->where('order_id', $hpItem->order_id)->first()->nomor ?? null;
                $opt = Jurnal::where('no_bg', $hpItem->no_bg_opt)->where('tipe', 'JNL')->where('order_id', $hpItem->order_id)->first()->nomor ?? null;
                $ut = Jurnal::where('no_bg', $hpItem->no_bg_ut)->where('tipe', 'JNL')->where('order_id', $hpItem->order_id)->first()->nomor ?? null;
                $hpItem->update([
                    'jurnal_opp' => $opp,
                    'jurnal_opt' => $opt,
                    'jurnal_ut' => $ut,
                ]);
            }
        }); // end transaction
    } catch (\Exception $e) {
        // Kembalikan error ke user (sama pola return sebelumnya)
      return redirect()
    ->route('hutang-pelayaran.index');
    }

    return redirect()->route('hutang-pelayaran.print', ['invoice' => $code]);
}


    public function delete(Request $request)
    {
        $order_id = explode(',', $request->order_id);
        HutangPelayaran::whereIn('order_id',$order_id)->delete();
        return back()->with('success','Data berhasil dihapus');
    }

    public function tarik(Request $request)
    {
        $data = HutangPelayaran::where('invoice',$request->invoice)->get();
        $hp = $data->first();
        if($hp->no_bg_opp){
            Jurnal::where('no_bg',$hp->no_bg_opp)->delete();
        }
        if($hp->no_bg_opt){
            Jurnal::where('no_bg',$hp->no_bg_opt)->delete();
        }
        if($hp->no_bg_ut){
            Jurnal::where('no_bg',$hp->no_bg_ut)->delete();
        }
        HutangPelayaran::where('invoice',$request->invoice)->update([
            'invoice' => null,
            'tgl_invoice' => null,
            'status' => 0
        ]);
        return back()->with('success','Data berhasil ditarik!');
    }

    public function update(HutangPelayaran $hutangpelayaran, Request $request)
    {
        $data = $request->all();
        $hutangpelayaran->update($data);

        return back()->with('success', 'Data berhasil diupdate');
    }

    public function destroy(HutangPelayaran $hutangpelayaran)
    {
        $hutangpelayaran->delete();

        return back()->with('success', 'Data berhasil dihapus');
    }

    public function datatable()
    {
        $data = HutangPelayaran::all()->sortByDesc('created_at');

        return Datatables::of($data)
            ->addColumn('tarif_pelayaran_id', function ($data) {
                return $data->tarif_pelayaran->pelayaran->nama;
            })
            ->addColumn('order_id', function ($data) {
                return $data->order->job . '-' . sprintf('%02d', $data->no_job);
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function cetak_invoice(Request $request)
    {
        $ids = $request->order_id;
        $order_id = explode(',', $request->order_id);
        // // dd($request->all());

        if (count($order_id) <= 1 && $order_id[0] == "") {
            return back()->with('danger', 'Harap checklist terlebih dahulu!');
        }

        // $cek = HutangPelayaran::whereIn('order_id', $order_id)->get()->groupBy('pelayaran_id');
        $cek = Order::join('jadwal_kapal','jadwal_kapal.id','order.jadwal_kapal_id')->whereIn('order.id', $order_id)->get()->groupBy('jadwal_kapal.pelayaran_id');
        if(count($cek)>1){
            return back()->with('danger', 'Harap checklist pelayaran yang sama!');
        }
        $cek = HutangPelayaran::whereIn('order_id', $order_id)->where('is_lock',0)->get();
        if(count($cek)>0){
            return back()->with('danger', 'Harap lock harga terlebih dahulu!');
        }
        // $cek = Order::whereIn('id', $order_id)->get()->groupBy('jadwal_kapal_id');
        // if(count($cek)>1){
        //     return back()->with('danger', 'Data Kapal dan Voyage yang dipilih tidak sama!');
        // }
        $data = HutangPelayaran::join('order','order.id','hutang_pelayaran.order_id')->whereIn('hutang_pelayaran.order_id', $order_id)->orderBy('order.job')->orderBy('order.no_job')->get()->groupBy('order.job');
        $data_bl = HutangPelayaran::join('order','order.id','hutang_pelayaran.order_id')->whereIn('hutang_pelayaran.order_id', $order_id)->orderBy('order.job')->orderBy('order.no_job')->get()->groupBy('order.penerimabl');
        $pelayaran = HutangPelayaran::whereIn('order_id', $order_id)->first()->pelayaran;
        $hp = HutangPelayaran::whereIn('order_id', $order_id)->first();
        // $data = HutangPelayaran::whereIn('order_id', $order_id)->orderBy('created_at')->get()->groupBy('job');

        return view('admin.hutangpelayaran.invoice', compact('data','pelayaran','ids','hp','data_bl'));
    }

    public function cetak_invoice_get()
    {
        $order_id = request('order_id');
        $order = Order::where('id', $order_id)->first();
        if (!$order) {
            return back()->with('danger', 'Invoice Tidak ditemukan!');
        }
        $hutangpelayaran = HutangPelayaran::where('order_id', request('order_id'))->first();
        $nama = $hutangpelayaran->order->jadwal_kapal->pelayaran->nama;
        $data = Order::where('order_id', $order_id)->orderBy('tgl_muat')->get()->groupBy('job');
        return view('admin.hutangpelayaran.invoice', compact('order', 'data', 'nama'));
    }

    public function print()
    {
        $data = HutangPelayaran::with('order')->where('invoice',request('invoice'))->get();
        if($data->count()<=0){
            return back()->with('danger','Data tidak ditemukan!');
        }
        $hp = $data->first();
        $jobs = $data->groupBy('order.job');
        $jadwal_kapal = JadwalKapal::find($data->first()->order->jadwal_kapal_id);
        $opp = 0;
        $opt = 0;
        $ut = 0;
        if($hp->pph>0){
            $opp+=1;
        }
        if($hp->pembulatan!=0){
            $opp+=1;
        }
        if($hp->penambahan_nominal!=0){
            $ut+=1;
        }
        foreach ($jobs as $list){
            $a = $list->where('opp','>',0)->groupBy('opp')->count();
            $b = $list->where('thc','>',0)->groupBy('thc')->count();
            $c = $list->where('apbs','>',0)->groupBy('apbs')->count();
            $d = $list->where('cleaning','>',0)->groupBy('cleaning')->count();
            $e = $list->where('opp_stamp','>',0)->groupBy('opp_stamp')->count();
            $f = $list->where('lss','>',0)->groupBy('lss')->count();
            $g = $list->where('opt','>',0)->groupBy('opt')->count();
            $h = $list->where('opt_stamp','>',0)->groupBy('opt_stamp')->count();
            $i = $list->where('ut','>',0)->groupBy('ut')->count();
            $j = $list->where('bl','>',0)->count();
            $k = $list->where('ut_stamp','>',0)->groupBy('ut_stamp')->count();
            $l = $list->where('ut_cleaning','>',0)->groupBy('ut_cleaning')->count();
            $m = $list->where('hp_seal','>',0)->groupBy('hp_seal')->count();
            $n = $list->where('vgm','>',0)->groupBy('vgm')->count();
            if($a>0){
                $opp+=$a;
            }
            if($b>0){
                $opp+=$b;
            }
            if($c>0){
                $opp+=$c;
            }
            if($d>0){
                $opp+=$d;
            }
            if($e>0){
                $opp+=$e;
            }
            if($f>0){
                $opp+=$f;
            }
            if($g>0){
                $opt+=$g;
            }
            if($h>0){
                $opt+=$h;
            }
            if($i>0){
                $ut+=$i;
            }
            if($j>0){
                $ut+=1;
            }
            if($k>0){
                $ut+=$k;
            }
            if($l>0){
                $ut+=$l;
            }
            if($m>0){
                $opp+=$m;
            }
             if($n>0){
                $opp+=$n;
            }
        }
        // dd($jobs->count());
        return view('admin.hutangpelayaran.print', compact('data','jadwal_kapal','jobs','hp','opp','opt','ut'));
    }

    function groupByValue($array) {
        $groups = [];

        foreach ($array as $item) {
            $groups[$item][] = $item;
        }

        return array_values($groups);
    }

    // public function show()
    // {
    //     return view('admin.hutangpelayaran.invoice');
    // }
}
