@extends('layouts.admin')

@section('content')
    <div class="container mt-4">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-primary text-white py-3">
                <h4 class="mb-0">
                    <i class="bi bi-lock-fill me-2"></i> Kunci Jurnal per Periode
                </h4>
            </div>
            <div class="card-body">
                <div class="table-responsive mt-2">
                    <table id="tablePeriode" class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Periode</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($periodeJurnal as $i => $periode)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $periode->periode }}</td>
                                    <td>
                                        @if ($periode->kunci == 1)
                                            <span class="badge bg-success">Terkunci</span>
                                        @else
                                            <span class="badge bg-danger">Belum Terkunci</span>
                                        @endif

                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-warning toggle-status"
                                            data-periode="{{ $periode->periode_key }}">
                                            <i class="bi bi-lock"></i> Ubah Status
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Belum ada periode jurnal</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).on('click', '.toggle-status', function() {
            let periode = $(this).data('periode');
            if (confirm("Yakin ingin ubah status kunci jurnal untuk periode " + periode + " ?")) {
                $.ajax({
                    url: "{{ route('kunci-jurnal.toggle') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        periode: periode
                    },
                    success: function(res) {
                        alert(res.message);
                        location.reload();
                    }
                });
            }
        });
    </script>
@endsection
