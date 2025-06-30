<div class="row">
    <x-input :value="$jadwalkapal->kapal_id??old('kapal_id')" :col="6" :label="'Kapal'" :type="'select'" :options="$kapal" :name="'kapal_id'" :required="true"></x-input>
    <x-input :value="$jadwalkapal->voyage??old('voyage')" :col="6" :label="'Voyage'" :type="'text'" :name="'voyage'" :required="true"></x-input>
    <x-input :value="$jadwalkapal->pelayaran_id??old('pelayaran_id')" :col="6" :label="'Pelayaran'" :type="'select'" :options="$pelayaran" :name="'pelayaran_id'" :required="true"></x-input>
    <x-input :value="$jadwalkapal->rute??old('rute')" :col="6" :label="'Rute'" :type="'text'" :name="'rute'" :required="true"></x-input>
    <x-input :value="$jadwalkapal->closing??old('closing')" :col="6" :label="'Closing'" :type="'date'" :name="'closing'"></x-input>
    <x-input :value="$jadwalkapal->etd??old('etd')" :col="6" :label="'ETD'" :type="'date'" :name="'etd'"></x-input>
    <x-input :value="$jadwalkapal->eta??old('eta')" :col="6" :label="'ETA'" :type="'date'" :name="'eta'"></x-input>
    @if ($jadwalkapal)
        @if ($jadwalkapal->hasInvoice())
            @if (Auth::user()->role_id==1)
                <x-input :value="$jadwalkapal->td??old('td')" :col="6" :label="'TD'" :type="'date'" :name="'td'"></x-input>
            @else
                @if (is_null($jadwalkapal->td) && Auth::id()==5)
                    <x-input :value="$jadwalkapal->td??old('td')" :col="6" :label="'TD'" :type="'date'" :name="'td'"></x-input>
                @endif
            @endif
        @else
            <x-input :value="$jadwalkapal->td??old('td')" :col="6" :label="'TD'" :type="'date'" :name="'td'"></x-input>
        @endif
    @else
    <x-input :value="$jadwalkapal->td??old('td')" :col="6" :label="'TD'" :type="'date'" :name="'td'"></x-input>
    @endif
    {{-- @if (!empty($jadwalkapal->td))
        @if (Auth::user()->role_id==1)
            <x-input :value="$jadwalkapal->td??old('td')" :col="6" :label="'TD'" :type="'date'" :name="'td'"></x-input>
        @endif
    @else
    @endif --}}
    {{-- <x-input :value="$jadwalkapal->ba_kirim??old('ba_kirim')" :col="6" :label="'Ba Kirim'" :type="'date'" :name="'ba_kirim'"></x-input> --}}
    <x-input :value="$jadwalkapal->keterangan??old('keterangan')" :col="12" :label="'Keterangan'" :type="'textarea'" :name="'keterangan'" :required="true"></x-input>
    <div class="col-12 mb-2 px-1">
        <button type="submit" class="btn btn-success btn-sm">{{ empty($jadwalkapal)?'Tambah':'Update' }} Data</button>
    </div>
</div>
