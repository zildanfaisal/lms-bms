@extends('layouts.master')

@section('title', 'Tambah Karyawan')

@section('header')
    <h1 class="text-xl font-semibold text-gray-800">Tambah Karyawan</h1>
@endsection

@push('scripts')
<script>
// Dependent selects: direktorat -> divisi -> unit
document.addEventListener('DOMContentLoaded', function () {
    const direktoratSelect = document.querySelector('select[name="direktorat_id"]');
    const divisiSelect = document.querySelector('select[name="divisi_id"]');
    const unitSelect = document.querySelector('select[name="unit_id"]');

    function clearTomOptions(sel) {
        if (!sel) return;
        if (sel.tomselect) sel.tomselect.clearOptions();
        sel.innerHTML = '<option value="">-- Pilih --</option>';
    }

    function addOptionsToTom(sel, items, valueKey, textKey) {
        if (!sel) return;
        if (sel.tomselect) {
            sel.tomselect.clearOptions();
            sel.tomselect.addOption([{value: '', text: '-- Pilih --'}]);
            items.forEach(function(it) {
                sel.tomselect.addOption({value: it[valueKey], text: it[textKey]});
            });
            sel.tomselect.refreshOptions(false);
        } else {
            sel.innerHTML = '<option value="">-- Pilih --</option>' + items.map(it => `<option value="${it[valueKey]}">${it[textKey]}</option>`).join('');
        }
    }

    if (direktoratSelect) {
        direktoratSelect.addEventListener('change', function () {
            const id = this.value;
            clearTomOptions(divisiSelect);
            clearTomOptions(unitSelect);
            if (!id) return;
            fetch("{{ url('/ajax/divisi-by-direktorat') }}/" + id)
                .then(r => r.json())
                .then(data => {
                    addOptionsToTom(divisiSelect, data, 'id', 'nama_divisi');
                }).catch(console.error);
        });
    }

    if (divisiSelect) {
        divisiSelect.addEventListener('change', function () {
            const id = this.value;
            clearTomOptions(unitSelect);
            if (!id) return;
            fetch("{{ url('/ajax/unit-by-divisi') }}/" + id)
                .then(r => r.json())
                .then(data => {
                    addOptionsToTom(unitSelect, data, 'id', 'nama_unit');
                }).catch(console.error);
        });
    }
});
</script>
@endpush

@section('content')
    <div class="bg-white rounded-xl shadow p-6">
        @if ($errors->any())
            <div class="mb-4 text-red-600">
                <ul class="list-disc ps-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('karyawan.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm text-gray-700">NIK</label>
                    <input type="text" name="nik" value="{{ old('nik') }}" class="mt-1 block w-full rounded border-gray-200" required>
                </div>

                <div>
                    <label class="block text-sm text-gray-700">Nama</label>
                    <input type="text" name="nama" value="{{ old('nama') }}" class="mt-1 block w-full rounded border-gray-200" required>
                </div>

                <div>
                    <label class="block text-sm text-gray-700">E-mail</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="mt-1 block w-full rounded border-gray-200" required>
                    <p class="text-xs text-gray-500 mt-1">Password default <code>BimasaktiJaya123</code>.</p>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm text-gray-700">Direktorat</label>
                        <select name="direktorat_id" class="mt-1 block w-full rounded border-gray-200 tom-select" required>
                            <option value="">-- Pilih Direktorat --</option>
                            @foreach($direktorats as $dir)
                                <option value="{{ $dir->id }}">{{ $dir->nama_direktorat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm text-gray-700">Divisi</label>
                        <select name="divisi_id" class="mt-1 block w-full rounded border-gray-200 tom-select" required>
                            <option value="">-- Pilih Divisi --</option>
                            @foreach($divisis as $div)
                                <option value="{{ $div->id }}">{{ $div->nama_divisi }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm text-gray-700">Unit</label>
                        <select name="unit_id" class="mt-1 block w-full rounded border-gray-200 tom-select" required>
                            <option value="">-- Pilih Unit --</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->nama_unit }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-gray-700">Jabatan</label>
                        <select name="jabatan_id" class="mt-1 block w-full rounded border-gray-200 tom-select" required>
                            <option value="">-- Pilih Jabatan --</option>
                            @foreach($jabatans as $j)
                                <option value="{{ $j->id }}">{{ $j->kode_jabatan }} - {{ $j->nama_jabatan }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm text-gray-700">Posisi (opsional)</label>
                        <select name="posisi_id" class="mt-1 block w-full rounded border-gray-200 tom-select">
                            <option value="">-- Pilih Posisi --</option>
                            @foreach($posisis as $p)
                                <option value="{{ $p->id }}">{{ $p->nama_posisi }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-gray-700">Status Karyawan</label>
                            <select name="status_karyawan" class="mt-1 block w-full rounded border-gray-200">
                                <option value="Tetap" {{ old('status_karyawan', 'Tetap') == 'Tetap' ? 'selected' : '' }}>Tetap</option>
                                <option value="Probation" {{ old('status_karyawan') == 'Probation' ? 'selected' : '' }}>Probation</option>
                                <option value="Magang" {{ old('status_karyawan') == 'Magang' ? 'selected' : '' }}>Magang</option>
                                <option value="Kontrak" {{ old('status_karyawan') == 'Kontrak' ? 'selected' : '' }}>Kontrak</option>
                                <option value="Lainnya" {{ old('status_karyawan') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                    </div>

                    <div>
                        <label class="block text-sm text-gray-700">No WhatsApp</label>
                        <input type="text" name="no_wa" value="{{ old('no_wa') }}" class="mt-1 block w-full rounded border-gray-200">
                    </div>
                </div>

                <div>
                    <label class="block text-sm text-gray-700">Tanggal Masuk</label>
                    <input type="date" name="tanggal_masuk" value="{{ old('tanggal_masuk') }}" class="mt-1 block w-full rounded border-gray-200">
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('karyawan.index') }}" class="inline-flex items-center px-3 py-2 rounded bg-gray-200 text-sm">Batal</a>
                    <button type="submit" class="inline-flex items-center px-3 py-2 rounded bg-purple-600 text-white text-sm">Simpan</button>
                </div>
            </div>
        </form>
    </div>
@endsection
