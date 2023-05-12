@extends('layouts.admin')
@section('content')
<div class="container">
    <div class="card p-3 shadow">
        <h4>Estimasi HPP</h4>
        <hr>
        <livewire:estimasi-hpp/>
    </div>
</div>
@endsection

@section('script')
    <script>
        // $("#pelayaran").select2();
        // $("#stuffing").select2();
        // $("#cont").select2();
        // $("#dari").select2();
        // $("#tujuan").select2();
    </script>
@endsection

