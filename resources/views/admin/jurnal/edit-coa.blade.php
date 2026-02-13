@extends('layouts.admin')

@section('style')
<style>
    input, select, table {
        font-size: 0.75rem !important;
    }
    table {
        table-layout: 100% !important;
        width: 100%;
    }
    .table th, .table td {
        padding: 0.35rem 0.5rem !important;
        font-size: 0.75rem !important;
        vertical-align: middle;
        white-space: nowrap;
    }
    .form-control, .form-select {
        padding: 0.25rem 0.4rem !important;
        height: auto;
    }
    label {
        font-size: 0.8rem;
        margin-bottom: 0.2rem;
    }

    .select2-container {
    width: 100% !important;
}

</style>
@endsection



@section('content')
<div class="container mt-4">
    <h5>Edit Jurnal - COA</h5>
    <hr>

    <form action="{{ route('jurnal.update.coa', $data[0]) }}" method="POST" id="form-coa">
        @csrf
        @method('PUT')

        <div class="row mb-3">
            <div class="col-md-4">
                <label>Nomor Jurnal</label>
                <input type="text" class="form-control" value="{{ $data[0]->nomor }}" disabled>
            </div>
            <div class="col-md-4">
                <label>Tanggal</label>
                <input type="date" name="created_at" class="form-control" readonly value="{{ date('Y-m-d', strtotime($data[0]->created_at)) }}">
            </div>
        </div>

       <table class="table table-bordered">
    <thead class="table-light">
        <tr>
            <th>ID</th>
            <th>COA</th>
            <th>No JOB</th>
            <th>Container</th>
            <th>Nopol</th>
            <th>Keterangan</th>
            <th>Debit</th>
            <th>Credit</th>
            <th>No BG</th>
            <th>Invoice</th>
            <th>Invoice External</th>
            <th>Invoice Agen</th>
            <th>Invoice Vendor</th>
            <th>Invoice Trucking</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $j)
        <tr>
              <td>{{ $j->id }}</td>
          <td>
   <select class="form-select select2" name="jurnal[{{ $j->id }}][coa_id]" 
    style="width: 100%" 
    data-id="{{ $j->id }}"
    data-original="{{ $j->coa_id }}">
        <option value="">-- Pilih COA --</option>
        @foreach($coa as $c)
            <option value="{{ $c->id }}" {{ $j->coa_id == $c->id ? 'selected' : '' }}>
                {{ $c->kode }} - {{ $c->nama }}
            </option>
        @endforeach
    </select>
</td>
           <td>
    @if ($j->order)
        {{ $j->order->job }} - {{  sprintf('%02d', $j->order->no_job) }}
    @else
        -
    @endif
</td>

            <td>{{ $j->container ?? '-' }}</td>
            <td>
                {{
                    $j->order_trucking?->kendaraan
                        ? $j->order_trucking->kendaraan->nopol . ' | ' . $j->order_trucking->kendaraan->milik
                        : ($j->nopol . ' | ' . '-' ?? '-')
                }}
            </td>
            <td>{{ $j->nama }}</td>
            <td>{{ number_format($j->debit) }}</td>
            <td>{{ number_format($j->credit) }}</td>
            <td>{{ $j->no_bg }}</td>
            <td>{{ $j->invoice }}</td>
            <td>{{ $j->invoice_external }}</td>
            <td>{{ $j->invoice_agen }}</td>
            <td>{{ $j->invoice_vendor }}</td>
            <td>{{ $j->invoice_trucking }}</td>
        </tr>
        @endforeach
    </tbody>
</table>


        <div class="mt-3">
            <button class="btn btn-primary">Simpan Perubahan</button>
            {{-- <a href="{{ route('jurnal.index') }}" class="btn btn-danger">Close</a> --}}
        </div>
    </form>
</div>
@endsection

@section('script')
    <script>
       $('.select2').select2({
    width: '100%'
});

$('#form-coa').on('submit', function(e) {
    // Cegah submit default dulu
    e.preventDefault();

    // Hapus input yang belum berubah
    $('select[name^="jurnal"]').each(function() {
        const original = $(this).data('original');
        const current = $(this).val();
        if (original == current || current === '') {
            $(this).removeAttr('name'); // tidak dikirim
        }
    });

    this.submit(); // submit form setelah filter
});


        var total_credit = 0;
        var total_debit = 0;
        function uncheck (e,id) {
            if($('#' + id).is(":checked")){
                $('#job-'+id).attr('disabled',false);
                $('#coa_id-'+id).attr('disabled',false);
                $('#nama-'+id).attr('disabled',false)
                $('#amount-'+id).attr('disabled',false)
                $('#debit-'+id).attr('disabled',false)
                $('#credit-'+id).attr('disabled',false)
            }else{
                $('#job-'+id).attr('disabled',true);
                $('#coa_id-'+id).attr('disabled',true);
                $('#nama-'+id).attr('disabled',true)
                $('#amount-'+id).attr('disabled',true)
                $('#debit-'+id).attr('disabled',true)
                $('#credit-'+id).attr('disabled',true)
            }
            total();
        }

        function total(){
            var check = $("input[name='id[]']").map(function(){
                if($(this).is(":checked")){
                    return $(this).val();
                }
            }).get();
            total_credit = 0;
            total_debit = 0;
            for (let i = 0; i < check.length; i++) {
                const item = check[i];
                var d = parseInt($('#debit-'+item).val());
                var c = parseInt($('#credit-'+item).val());
                if(d!=""){
                    total_debit+=d;
                }
                if(c!=""){
                    total_credit+=c;
                }
            }
            $('#total_debit').html('Rp. '+total_debit.toLocaleString('en-US'));
            $('#total_credit').html('Rp. '+total_credit.toLocaleString('en-US'));
        }

        $('#btn-save').click(function (e) {
            if(total_debit!=total_credit){
                alert('Jurnal Tidak Balance debit = '+total_debit+' & credit = '+total_credit+' ! Harap check lagi')
            }else{
                if(confirm('are you sure')){
                    $('#form-jurnal').submit();
                }
            }
        });
    </script>
@endsection

