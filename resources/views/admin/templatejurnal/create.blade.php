@extends('layouts.admin')
@section('content')
    <div class="container mt-3">
        <div class="card">
            <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
                <div class="card-title">Template Jurnal</div>
            </div>
            <div class="card-body">
                <span>PARAM</span>
                <div class="d-flex flex-wrap gap-2" style="white-space: nowrap">
                    <span class="bg-light-primary px-2 py-1">[1] ID JOB</span>
                    <span class="bg-light-primary px-2 py-1">[2] Cont (XPDC)</span>
                    <span class="bg-light-primary px-2 py-1">[3] Seal (XPDC)</span>
                    <span class="bg-light-primary px-2 py-1">[4] Kapal (XPDC)</span>
                    <span class="bg-light-primary px-2 py-1">[5] Voyage (XPDC)</span>
                    <span class="bg-light-primary px-2 py-1">[6] Shipment (XPDC)</span>
                    <span class="bg-light-primary px-2 py-1">[7] Pembayar (XPDC)</span>
                    <span class="bg-light-primary px-2 py-1">[8] Customer (TRUCKING)</span>
                    <span class="bg-light-primary px-2 py-1">[9] Shipment (TRUCKING)</span>
                    <span class="bg-light-primary px-2 py-1">[10] Tujuan (TRUCKING)</span>
                </div>
            </div>
        </div>
        <livewire:create-template-jurnal template_id="{{ request('template_id') }}"/>
    </div>
@endsection

@section('script')
    <script>
        $('.select2').select2();
    </script>
@endsection
