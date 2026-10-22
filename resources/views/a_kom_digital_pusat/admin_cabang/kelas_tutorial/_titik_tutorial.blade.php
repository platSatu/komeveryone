{{--
    kom_v02: pilihan "Titik Kelas Tutorial" (bergantung pada modul).
    - Akhir Modul (selalu ada)  → value kosong
    - Setelah Video N           → value = id video yang dicentang superadmin
--}}
@php
    $modulTerpilih = \App\Models\Kmd\KmdModuls::find(old('modul_id', $modulIdTerpilih ?? null));
    $titikAwal = $modulTerpilih ? app(\App\Services\Kmd\TitikTutorialService::class)->titikModul($modulTerpilih) : collect();
    $videoTerpilih = old('video_id', $videoIdTerpilih ?? '');
@endphp
<div class="form-group">
    <label for="video_id">{{ __('Titik Kelas Tutorial') }}</label>
    <select name="video_id" id="video_id" class="form-control">
        @forelse ($titikAwal as $t)
            <option value="{{ $t['video_id'] ?? '' }}" {{ (string) $videoTerpilih === (string) ($t['video_id'] ?? '') ? 'selected' : '' }}>
                {{ $t['label'] }}{{ $t['video'] ? ' — ' . $t['video']->name : '' }}
            </option>
        @empty
            <option value="">{{ __('Akhir Modul') }}</option>
        @endforelse
    </select>
    <small class="text-muted">
        {{ __('Pilih kapan kelas tutorial ini diikuti peserta: di tengah modul (setelah video tertentu) atau di akhir modul.') }}
    </small>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var modul = document.getElementById('modul_id');
        var titik = document.getElementById('video_id');
        if (!modul || !titik) return;

        modul.addEventListener('change', function () {
            titik.innerHTML = '<option value="">{{ __('Memuat...') }}</option>';
            fetch('{{ route('kom.kelas_tutor_cabang.api.titik.byModul') }}?modul_id=' + encodeURIComponent(modul.value), {
                headers: { 'Accept': 'application/json' }
            })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    titik.innerHTML = '';
                    if (!data.length) {
                        titik.innerHTML = '<option value="">{{ __('Akhir Modul') }}</option>';
                        return;
                    }
                    data.forEach(function (t) {
                        var opt = document.createElement('option');
                        opt.value = t.value;
                        opt.textContent = t.label;
                        titik.appendChild(opt);
                    });
                })
                .catch(function () {
                    titik.innerHTML = '<option value="">{{ __('Akhir Modul') }}</option>';
                });
        });
    });
</script>
