@extends('layouts.admin')
@section('content')
    <div class="container">
        <div class="row">
            <livewire:jurnal/>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $('.select2').select2();
    </script>
@endsection
