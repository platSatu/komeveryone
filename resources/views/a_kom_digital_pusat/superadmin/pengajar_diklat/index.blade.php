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

    @if ($errors->any())
        <div class="kom-note kom-note-danger mb-3">@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
    @endif

    <p class="kom-text mb-3">
        Pengajar di bawah ini muncul di form <strong>Buat Kelas Tutorial</strong> admin cabang sesuai kategorinya:
        <strong>Modul 110 &amp; 130 → Doktrinal</strong>, <strong>Modul 120 &amp; 140 → Praktikal</strong>.
        Sumbernya: sudah <em>selesai</em> Diklat Tutor (setelah End Zoom), atau ditandai manual di halaman ini.
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
                        <label for="category_diklat">Kategori Diklat</label>
                        <select name="category_diklat" id="category_diklat" class="form-control" required {{ $tabelSiap ? '' : 'disabled' }}>
                            <option value="">-- Pilih Kategori --</option>
                            <option value="Doktrinal" {{ old('category_diklat') === 'Doktrinal' ? 'selected' : '' }}>Doktrinal (Modul 110 &amp; 130)</option>
                            <option value="Praktikal" {{ old('category_diklat') === 'Praktikal' ? 'selected' : '' }}>Praktikal (Modul 120 &amp; 140)</option>
                        </select>
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
                    <h5 class="kom-card-title mb-0">Pengajar yang Lulus Diklat</h5>
                    <div class="small">
                        @foreach ($ringkasan as $k => $n)
                            <span class="badge badge-{{ $k === 'Doktrinal' ? 'primary' : 'success' }} mr-1">{{ $k }}: {{ $n }}</span>
                        @endforeach
                    </div>
                </div>

                <form method="GET" class="form-row mb-2">
                    <div class="col-12 col-md-5 mb-2">
                        <input type="text" name="cari" class="form-control form-control-sm" placeholder="Cari nama / email" value="{{ $cari }}">
                    </div>
                    <div class="col-8 col-md-4 mb-2">
                        <select name="kategori" class="form-control form-control-sm">
                            <option value="">Semua kategori</option>
                            <option value="Doktrinal" {{ $kategoriFilter === 'Doktrinal' ? 'selected' : '' }}>Doktrinal</option>
                            <option value="Praktikal" {{ $kategoriFilter === 'Praktikal' ? 'selected' : '' }}>Praktikal</option>
                        </select>
                    </div>
                    <div class="col-4 col-md-3 mb-2">
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
                                <th class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($daftar as $row)
                                <tr>
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
                                    <td class="text-right">
                                        @if ($row->manual)
                                            <form method="POST" action="{{ route('kom.super_pengajar_diklat.destroy', $row->manual->id) }}"
                                                  data-pesan="Cabut tanda manual {{ $row->user->name }} ({{ $row->kategori }})?{{ $row->diklat ? '' : ' Pengajar ini tidak akan muncul lagi di form Buat Kelas Tutorial.' }}"
                                                  onsubmit="return confirm(this.dataset.pesan);">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger">Cabut</button>
                                            </form>
                                        @else
                                            <span class="small text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted py-3">Tidak ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@include('a_kom_digital_pusat.katalog._style')
@endsection
