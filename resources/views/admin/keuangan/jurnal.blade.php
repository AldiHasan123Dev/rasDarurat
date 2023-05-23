@extends('layouts.admin')
@section('style')
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.6.1/css/select.dataTables.min.css">
    <style>
        table.dataTable tbody th, table.dataTable tbody td{
            padding: 0px 10px !important;
        }
    </style>
@endsection
@section('content')
    <div class="container">
        <div class="row">
            <div class="col">
                <table>
                    <thead>
                        <tr>
                            <td>[1] ID JOB</td>
                            <td>[2] CONTAINER </td>
                            <td>[3] SEAL </td>
                            <td>[3] CUSTOMER EKSPEDISI </td>
                            <td>[3] CUSTOMER TRUCKING </td>
                            <td>[3] CUSTOMER  </td>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection
