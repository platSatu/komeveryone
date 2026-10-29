{{--
    29 Okt 2026: dropdown Pengajar kelas tutorial (dipakai create & edit).
    Hanya pengajar yang sudah lulus Diklat Tutor sesuai kategori modul:
    Modul 110 & 130 = Doktrinal, Modul 120 & 140 = Praktikal
    (lihat App\Services\Kmd\PengajarDiklatService). Ikut berubah saat modul diganti.
--}}
@php
    $pengajarTerpilih = old('pengajar_id', $pengajarTerpilih ?? '');
@endphp
<div class="form-group">
    <label for="pengajar_id">Pengajar</label>
    <select name="pengajar_id" id="pengajar_id" class="form-control" required>
        <option value="">-- Pilih Pengajar --</option>
        @foreach ($pengajars as $pengajar)
            <option value="{{ $pengajar->id }}" {{ (string) $pengajarTerpilih === (string) $pengajar->id ? 'selected' : '' }}>
                {{ $pengajar->name }}
            </option>
        @endforeach
        @if ($pengajars->isEmpty())
            <option value="" disabled>{{ $pengajarKosong }}</option>
        @endif
    </select>
    <small id="pengajar_info" class="form-text {{ $pengajars->isEmpty() ? 'text-danger' : 'text-muted' }}">
        @if ($pengajars->isEmpty())
            {{ $pengajarKosong }}
        @else
            Menampilkan {{ $pengajars->count() }} pengajar yang sudah lulus Diklat Tutor {{ $category }}.
        @endif
    </small>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var modul = document.getElementById('modul_id');
        var pengajar = document.getElementById('pengajar_id');
        var info = document.getElementById('pengajar_info');
        if (!modul || !pengajar) return;

        function tulisInfo(teks, bahaya) {
            info.textContent = teks;
            info.className = 'form-text ' + (bahaya ? 'text-danger' : 'text-muted');
        }

        modul.addEventListener('change', function () {
            pengajar.innerHTML = '<option value="">Memuat pengajar...</option>';
            fetch('{{ route('kom.kelas_tutor_cabang.api.pengajars.byModul') }}?modul_id=' + encodeURIComponent(modul.value), {
                headers: { 'Accept': 'application/json' }
            })
                .then(function (r) { if (!r.ok) throw new Error(r.status); return r.json(); })
                .then(function (data) {
                    pengajar.innerHTML = '<option value="">-- Pilih Pengajar --</option>';
                    if (!data.pengajar || !data.pengajar.length) {
                        var kosong = document.createElement('option');
                        kosong.value = '';
                        kosong.disabled = true;
                        kosong.textContent = data.pesan_kosong;
                        pengajar.appendChild(kosong);
                        tulisInfo(data.pesan_kosong, true);
                        return;
                    }
                    data.pengajar.forEach(function (p) {
                        var opt = document.createElement('option');
                        opt.value = p.id;
                        opt.textContent = p.name;
                        pengajar.appendChild(opt);
                    });
                    tulisInfo('Menampilkan ' + data.pengajar.length + ' pengajar yang sudah lulus Diklat Tutor ' + data.kategori + '.', false);
                })
                .catch(function () {
                    pengajar.innerHTML = '<option value="">-- Pilih Pengajar --</option>';
                    tulisInfo('Gagal memuat daftar pengajar. Silakan pilih ulang modul.', true);
                });
        });
    });
</script>
