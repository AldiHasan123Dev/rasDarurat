@extends('layouts.admin')
@section('style')
    <style>
        @media print {
            @import url('https://fonts.cdnfonts.com/css/dot-matrix');
            body * {
                visibility: hidden;
                font-family: 'Dot Matrix', sans-serif;
                color: #000;
            }
            #print, #print * {
                visibility: visible;
                font-size: .7rem !important;
            }
            #print {
                width: 100%;
                position: absolute;
                left: 0;
                top: -70px;
            }
            #table td, #table th{
                border: 1px solid black;
            }
            #print {
                color: #000;
            }
        }
    </style>
@endsection
@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <button class="btn btn-sm btn-success my-2" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
            <div id="print">
                <div id="response">LOADING. . .</div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
    <script>
        $.ajax({
            type: "POST",
            url: "{{ route('api.neraca.jurnal') }}",
            data: {
                month:@json($month),
                year:@json($year),
            },
            success: function (response) {
                console.log(response);
                $('#response').html(response);
            },
            error: function (error) {
                console.log(error);
            }
        });
    </script>
@endsection
