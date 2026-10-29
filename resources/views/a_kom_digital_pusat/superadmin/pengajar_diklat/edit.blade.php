@extends('layouts.KomDigital')

@section('page_title', 'Edit Pengajar Diklat')

{{--
    29 Okt 2026 — atur semua kategori diklat satu pengajar sekaligus
    (mis. pengajar pindah dari Doktrinal ke Praktikal, atau mengajar keduanya).
--}}
@section('content')
<div class="kom-page container-fluid px-0">
    @include('a_kom_digital_pusat.katalog._flash')

    @if ($errors->any())
        <div class="kom-note kom-note-danger mb-3">@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
    @endif

    <div class="row justify-content-center">
        <div class="col-12 col-xl-9">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3" style="gap:8px">
                <div>
                    <h4 class="mb-0">{{ $pengajar->name }}</h4>
                    @if ($pengajar->email)<small class="text-muted">{{ $pengajar->email }}</small>@endif
                </div>
                <a href="{{ route('kom.super_pengajar_diklat.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
            </div>

            <p class="kom-text mb-3">
                Pengajar muncul di form <strong>Buat Kelas Tutorial</strong> untuk sebuah kategori kalau
                <strong>sudah diklat</strong> (selesai diklat atau ditandai manual) <strong>dan Aktif</strong>.
                Untuk pengajar yang pindah kategori: nonaktifkan / hapus centang kategori lama, lalu centang kategori baru.
                Kelas tutorial yang sudah dibuat tidak berubah.
            </p>

            <form method="POST" action="{{ route('kom.super_pengajar_diklat.update', $pengajar->id) }}">
                @csrf
                @method('PUT')

                <div class="row">
                    @foreach ($kategori as $k => $st)
                        @php
                            $manual = (bool) old("kategori.$k.manual", $st->manual);
                            $aktif = (string) old("kategori.$k.aktif", $st->aktif ? '1' : '0') === '1';
                            $modul = $k === 'Doktrinal' ? 'Modul 110 & 130' : 'Modul 120 & 140';
                        @endphp
                        <div class="col-12 col-md-6 mb-3">
                            <div class="kom-card h-100 js-kategori">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="kom-card-title mb-0">
                                        <span class="badge badge-{{ $k === 'Doktrinal' ? 'primary' : 'success' }}">{{ $k }}</span>
                                        <small class="text-muted d-block mt-1" style="font-size:12px">{{ $modul }}</small>
                                    </h5>
                                    <span class="badge js-hasil"></span>
                                </div>

                                <div class="mb-2 small">
                                    Diklat aplikasi:
                                    @if ($st->diklat)
                                        <span class="badge badge-light border js-diklat" data-diklat="1">Sudah selesai</span>
                                    @else
                                        <span class="text-muted js-diklat" data-diklat="0">Belum pernah</span>
                                    @endif
                                </div>

                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="hidden" name="kategori[{{ $k }}][manual]" value="0">
                                    <input type="checkbox" class="custom-control-input js-manual" id="manual_{{ $k }}"
                                           name="kategori[{{ $k }}][manual]" value="1" {{ $manual ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="manual_{{ $k }}">Tandai manual sudah diklat {{ $k }}</label>
                                </div>

                                <div class="form-group mb-2">
                                    <label class="small mb-1" for="aktif_{{ $k }}">Status</label>
                                    <select name="kategori[{{ $k }}][aktif]" id="aktif_{{ $k }}" class="form-control form-control-sm js-aktif">
                                        <option value="1" {{ $aktif ? 'selected' : '' }}>Aktif</option>
                                        <option value="0" {{ ! $aktif ? 'selected' : '' }}>Nonaktif</option>
                                    </select>
                                </div>

                                <div class="form-group mb-2 js-alasan-wrap">
                                    <label class="small mb-1" for="alasan_{{ $k }}">Alasan nonaktif <span class="text-danger">*</span></label>
                                    <input type="text" name="kategori[{{ $k }}][alasan]" id="alasan_{{ $k }}" maxlength="255"
                                           class="form-control form-control-sm" value="{{ old("kategori.$k.alasan", $st->alasan) }}"
                                           placeholder="mis. pindah ke kategori Praktikal">
                                    @if ($st->nonaktif_at)
                                        <small class="text-muted">Dinonaktifkan {{ $st->nonaktif_at->format('d/m/Y H:i') }}{{ $st->penonaktif ? ' oleh '.$st->penonaktif : '' }}</small>
                                    @endif
                                </div>

                                <div class="form-group mb-0">
                                    <label class="small mb-1" for="catatan_{{ $k }}">Catatan <small class="text-muted">(opsional)</small></label>
                                    <input type="text" name="kategori[{{ $k }}][catatan]" id="catatan_{{ $k }}" maxlength="255"
                                           class="form-control form-control-sm" value="{{ old("kategori.$k.catatan", $st->catatan) }}">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('kom.super_pengajar_diklat.index') }}" class="btn btn-light">Batal</a>
            </form>
        </div>
    </div>
</div>

@include('a_kom_digital_pusat.katalog._style')
<script>
    // Tampilkan alasan hanya saat Nonaktif, dan ringkasan apakah muncul di form Buat Kelas Tutorial.
    document.querySelectorAll('.js-kategori').forEach(function (kartu) {
        var manual = kartu.querySelector('.js-manual');
        var aktif = kartu.querySelector('.js-aktif');
        var alasanWrap = kartu.querySelector('.js-alasan-wrap');
        var alasan = alasanWrap.querySelector('input');
        var hasil = kartu.querySelector('.js-hasil');
        var diklat = kartu.querySelector('.js-diklat').dataset.diklat === '1';

        function segarkan() {
            var nonaktif = aktif.value === '0';
            alasanWrap.style.display = nonaktif ? '' : 'none';
            alasan.required = nonaktif;

            var muncul = !nonaktif && (diklat || manual.checked);
            hasil.className = 'badge ' + (muncul ? 'badge-success' : 'badge-secondary');
            hasil.textContent = muncul ? 'Muncul di form' : 'Tidak muncul';
        }

        manual.addEventListener('change', segarkan);
        aktif.addEventListener('change', segarkan);
        segarkan();
    });
</script>
@endsection
