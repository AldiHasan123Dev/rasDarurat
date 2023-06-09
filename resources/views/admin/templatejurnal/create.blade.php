@extends('layouts.admin')
@section('content')
    <div class="container mt-3">
        <div class="card">
            <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
                <div class="card-title">Buat Template Jurnal</div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered" style="font-size:.7rem">
                        <tr>
                            <td class="fw-bold">Param</td>
                            <td>[1] Pembayar (XPDC)</td>
                            <td>[2] Pengirim (XPDC)</td>
                            <td>[3] Penerima (XPDC)</td>
                            <td>[4] Pelayaran (XPDC)</td>
                            <td>[5] Customer (TRUCKING)</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Contoh Output</td>
                            <td>AGRINDO PT</td>
                            <td>CIPTA MAKMUR</td>
                            <td>AGEN</td>
                            <td>PT. TANTO INTIM LINE</td>
                            <td>BP. YANSEN</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <livewire:create-template-jurnal />
    </div>
@endsection

@section('script')
    <script>
        $('.select2').select2();
    </script>
@endsection
