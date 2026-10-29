@extends('layouts.KomDigital')

@section('page_title', 'Pengajar Diklat')

{{--
    29 Okt 2026 — Superadmin › Diklat Tutor › Pengajar Diklat.
    Daftar pengajar yang muncul di form Buat Kelas Tutorial admin cabang
    (Modul 110 & 130 = Doktrinal, Modul 120 & 140 = Praktikal), plus form untuk
    menandai pengajar MANUAL sudah ikut diklat.
--}}
@section('content')
<div class="kom-page container-fluid px-0">
    @include('a_kom_digital_pusat.katalog._flash')

    @if (! $tabelSiap)
        <div class="kom-note kom-note-danger mb-3">Tabel pengajar diklat manual belum dibuat. Jalankan <code>php8.4 artisan migrate --force</code> di server.</div>
    @endif

    @if ($tabelSiap && ! $statusSiap)
        <div class="kom-note kom-note-danger mb-3">Kolom status belum dibuat, tombol Aktifkan/Nonaktifkan belum bisa dipakai. Jalankan <code>php8.4 artisan migrate --force</code> di server.</div>
    @endif

    @if ($errors->any())
        <div class="kom-note kom-note-danger mb-3">@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
    @endif

    <p class="kom-text mb-3">
        Pengajar di bawah ini muncul di form <strong>Buat Kelas Tutorial</strong> admin cabang sesuai kategorinya:
        <strong>Modul 110 &amp; 130 → Doktrinal</strong>, <strong>Modul 120 &amp; 140 → Praktikal</strong>.
        Sumbernya: sudah <em>selesai</em> Diklat Tutor (setelah End Zoom), atau ditandai manual di halaman ini.
        Pengajar yang <strong>dinonaktifkan</strong> tidak muncul di form tersebut (kelas yang sudah dibuat tidak berubah).
    </p>

    <div class="row">
        <div class="col-12 col-lg-4 mb-3">
            <div class="kom-card h-100">
                <h5 class="kom-card-title">Tandai Pengajar Sudah Diklat</h5>
                <form method="POST" action="{{ route('kom.super_pengajar_diklat.store') }}">
                    @csrf
                    <div class="form-group">
                        <label for="user_id">Pengajar</label>
                        <select name="user_id" id="user_id" class="form-control" required {{ $tabelSiap ? '' : 'disabled' }}>
                            <option value="">-- Pilih Pengajar --</option>
                            @foreach ($semuaPengajar as $p)
                                <option value="{{ $p->id }}" {{ old('user_id') === $p->id ? 'selected' : '' }}>{{ $p->name }}{{ $p->email ? ' — '.$p->email : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="d-block">Kategori Diklat <small class="text-muted">(boleh dua-duanya)</small></label>
                        @php $kategoriLama = (array) old('category_diklat', []); @endphp
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="kat_doktrinal" name="category_diklat[]" value="Doktrinal"
                                   {{ in_array('Doktrinal', $kategoriLama, true) ? 'checked' : '' }} {{ $tabelSiap ? '' : 'disabled' }}>
                            <label class="custom-control-label" for="kat_doktrinal">Doktrinal (Modul 110 &amp; 130)</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="kat_praktikal" name="category_diklat[]" value="Praktikal"
                                   {{ in_array('Praktikal', $kategoriLama, true) ? 'checked' : '' }} {{ $tabelSiap ? '' : 'disabled' }}>
                            <label class="custom-control-label" for="kat_praktikal">Praktikal (Modul 120 &amp; 140)</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="catatan">Catatan <small class="text-muted">(opsional)</small></label>
                        <input type="text" name="catatan" id="catatan" class="form-control" maxlength="255" value="{{ old('catatan') }}" placeholder="mis. Diklat offline Juli 2025">
                    </div>
                    <button type="submit" class="btn btn-primary btn-block" {{ $tabelSiap ? '' : 'disabled' }}>Simpan</button>
                    <small class="form-text text-muted mt-2">Hanya menambah tanda. Data diklat asli, persembahan, dan kelas yang sudah ada tidak berubah.</small>
                </form>
            </div>
        </div>

        <div class="col-12 col-lg-8 mb-3">
            <div class="kom-card h-100">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-2" style="gap:8px">
                    <h5 class="kom-card-title mb-0">Pengajar Diklat</h5>
                    <div class="small">
                        @foreach ($ringkasan as $k => $n)
                            <span class="badge badge-{{ $k === 'Doktrinal' ? 'primary' : 'success' }} mr-1" title="Pengajar aktif yang muncul di form Buat Kelas Tutorial">{{ $k }} aktif: {{ $n }}</span>
                        @endforeach
                    </div>
                </div>

                <form method="GET" class="form-row mb-2">
                    <div class="col-12 col-md-4 mb-2">
                        <input type="text" name="cari" class="form-control form-control-sm" placeholder="Cari nama / email" value="{{ $cari }}">
                    </div>
                    <div class="col-6 col-md-3 mb-2">
                        <select name="kategori" class="form-control form-control-sm">
                            <option value="">Semua kategori</option>
                            <option value="Doktrinal" {{ $kategoriFilter === 'Doktrinal' ? 'selected' : '' }}>Doktrinal</option>
                            <option value="Praktikal" {{ $kategoriFilter === 'Praktikal' ? 'selected' : '' }}>Praktikal</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-3 mb-2">
                        <select name="status" class="form-control form-control-sm">
                            <option value="">Semua status</option>
                            <option value="aktif" {{ $statusFilter === 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ $statusFilter === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-2 mb-2">
                        <button class="btn btn-sm btn-secondary btn-block">Filter</button>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-sm table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Kategori</th>
                                <th>Sumber</th>
                                <th>Status</th>
                                <th class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($daftar as $row)
                                <tr class="{{ $row->aktif ? '' : 'text-muted' }}">
                                    <td>
                                        {{ $row->user->name }}
                                        @if ($row->user->email)<div class="small text-muted">{{ $row->user->email }}</div>@endif
                                    </td>
                                    <td><span class="badge badge-{{ $row->kategori === 'Doktrinal' ? 'primary' : 'success' }}">{{ $row->kategori }}</span></td>
                                    <td>
                                        @if ($row->diklat)<span class="badge badge-light border">Diklat selesai</span>@endif
                                        @if ($row->manual)
                                            <span class="badge badge-warning">Manual</span>
                                            @if ($row->manual->catatan)<div class="small text-muted">{{ $row->manual->catatan }}</div>@endif
                                        @endif
                                    </td>
                                    <td>
                                        @if ($row->aktif)
                                            <span class="badge badge-success">Aktif</span>
                                        @else
                                            <span class="badge badge-danger">Nonaktif</span>
                                            @if ($row->baris?->alasan_nonaktif)
                                                <div class="small">{{ $row->baris->alasan_nonaktif }}</div>
                                            @endif
                                            <div class="small text-muted">
                                                {{ optional($row->baris?->nonaktif_at)->format('d/m/Y H:i') }}{{ $row->baris?->penonaktif ? ' · '.$row->baris->penonaktif->name : '' }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-right text-nowrap">
                                        @if ($statusSiap)
                                            <a href="{{ route('kom.super_pengajar_diklat.edit', $row->user->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                        @endif
                                        @if ($statusSiap)
                                            <form method="POST" action="{{ route('kom.super_pengajar_diklat.status') }}" class="d-inline js-status"
                                                  data-nama="{{ $row->user->name }}" data-kategori="{{ $row->kategori }}" data-aktif="{{ $row->aktif ? 0 : 1 }}">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="user_id" value="{{ $row->user->id }}">
                                                <input type="hidden" name="category_diklat" value="{{ $row->kategori }}">
                                                <input type="hidden" name="aktif" value="{{ $row->aktif ? 0 : 1 }}">
                                                <input type="hidden" name="alasan" value="">
                                                @if ($row->aktif)
                                                    <button class="btn btn-sm btn-outline-warning">Nonaktifkan</button>
                                                @else
                                                    <button class="btn btn-sm btn-outline-success">Aktifkan</button>
                                                @endif
                                            </form>
                                        @endif
                                        @if ($row->manual)
                                            <form method="POST" action="{{ route('kom.super_pengajar_diklat.destroy', $row->manual->id) }}" class="d-inline"
                                                  data-pesan="Cabut tanda manual {{ $row->user->name }} ({{ $row->kategori }})?{{ $row->diklat ? '' : ' Pengajar ini tidak akan muncul lagi di form Buat Kelas Tutorial.' }}"
                                                  onsubmit="return confirm(this.dataset.pesan);">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger">Cabut</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-3">Tidak ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@include('a_kom_digital_pusat.katalog._style')
<script>
    // Nonaktifkan wajib pakai alasan; aktifkan cukup konfirmasi.
    document.querySelectorAll('form.js-status').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            var nama = form.dataset.nama, kategori = form.dataset.kategori;
            if (form.dataset.aktif === '0') {
                var alasan = window.prompt('Alasan menonaktifkan ' + nama + ' (' + kategori + '):');
                if (alasan === null) { e.preventDefault(); return; }
                alasan = alasan.trim();
                if (!alasan) { e.preventDefault(); alert('Alasan wajib diisi.'); return; }
                form.querySelector('input[name="alasan"]').value = alasan;
            } else if (!window.confirm('Aktifkan kembali ' + nama + ' untuk ' + kategori + '?')) {
                e.preventDefault();
            }
        });
    });
</script>
@endsection
