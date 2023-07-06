<form action="{{ route('templatejurnal.store') }}" method="POST" class="mt-3 row flex-row-reverse">
    @csrf
    <input type="hidden" name="template_id" value="{{ request('template_id') }}">
    <div class="col-3">
        <div class="text-center border-bottom border-dark border-3">
            <span>2</span>
        </div>
        <div class="card shadow p-3 mt-2">
            <div class="mb-2">
                <label>Nama Template</label>
                <input type="text" name="name" required class="form-control" value="{{ $template->nama ?? '' }}">
            </div>
            <div class="mb-2">
                <button class="btn btn-sm btn-primary w-100 text-center" type="submit" onclick="return confirm('are you sure?')">Simpan Template</button>
            </div>
        </div>
    </div>
    <div class="col-9">
        <div class="text-center border-bottom border-dark border-3">
            <span>1</span>
        </div>
        <div class="card shadow p-3 mt-2">
            <table class="table table-sm" style="font-size: .7rem">
                <thead>
                    <tr>
                        <th>Akun Debit</th>
                        <th>Akun Kredit</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody id="tbody">
                    @if ($template)
                        @foreach ($template->template_items as $i => $tem)
                            <tr>
                                <td>
                                    <select class="form-control select2" name="debit_coa_id[]" style="font-size:.9rem !important; width:150px">
                                        <option value=""></option>
                                        @foreach ($coa as $item)
                                        <option {{ $tem->coa_debit_id==$item->id?'selected':'' }} value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control select2" name="credit_coa_id[]" style="font-size:.9rem !important; width:150px">
                                        <option value=""></option>
                                        @foreach ($coa as $item)
                                        <option {{ $tem->coa_credit_id==$item->id?'selected':'' }} value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input type="text" name="keterangan[]" style="width: 150px" value="{{ $tem->keterangan }}"></td>
                            </tr>
                        @endforeach
                    @else
                        @for ($i = 0; $i < $kolom; $i++)
                            <tr>
                                <td>
                                    <select class="form-control select2" name="debit_coa_id[]" style="font-size:.9rem !important; width:150px">
                                        <option value=""></option>
                                        @foreach ($coa as $item)
                                        <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control select2" name="credit_coa_id[]" style="font-size:.9rem !important; width:150px">
                                        <option value=""></option>
                                        @foreach ($coa as $item)
                                        <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input type="text" name="keterangan[]" style="width: 150px"></td>
                            </tr>
                        @endfor
                    @endif
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3">
                            <button class="btn btn-sm btn-success w-100 text-center" type="button" onclick="addColumn()">Tambah Kolom</button>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        function addColumn(){
            let html = `<tr>
                            <td style="width: 200px">
                                <select class="form-control select2" name="debit_coa_id[]" style="font-size:.9rem !important">
                                    <option value=""></option>
                                    @foreach ($coa as $item)
                                    <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td style="width: 200px">
                                <select class="form-control select2" name="credit_coa_id[]" style="font-size:.9rem !important">
                                    <option value=""></option>
                                    @foreach ($coa as $item)
                                    <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="text" name="keterangan[]" style="width: 100%"></td>
                        </tr>`;

            $('#tbody').append(html);
            setTimeout(() => {
                $('.select2').select2();
            }, 1000);
        }
    </script>
@endpush
