{{--
    kom_v02 — Pop-up "Pilih Bahasa Belajar".

    Logika TIDAK berubah: pilihan tetap disimpan lewat route kom.bahasa.store
    (aturan kunci bahasa, validasi, dan arah setelahnya sama dengan halaman Pilih Bahasa).

    Dua cara tampil ($mode):
      - 'baru'  : user BARU (bahasa belum pernah dipilih). Muncul otomatis setelah login,
                  tidak bisa ditutup, Bahasa Indonesia terpilih sebagai bawaan.
                  Setelah disimpan → lanjut ke Pilih Kelas.
      - 'kelas' : user yang sudah menyelesaikan kelas lalu klik "Mulai Kelas" (mis. KOM 200).
                  Dibuka dari tombol di katalog, boleh ditutup, bahasa yang sedang dipakai
                  terpilih sebagai bawaan. Setelah disimpan → lanjut ke input kode buku kelas tsb.
--}}
@php
    $mode = $mode ?? 'baru';
    $popupBahasaList = \App\Models\Kmd\Language::activeList();
    $popupBahasaDefault = old('language_code', $bahasaBawaan ?? \App\Models\Kmd\Language::defaultCode());
    $idPopup = $mode === 'kelas' ? 'komLangPopupKelas' : 'komLangPopupBaru';
@endphp
<div class="kom-lang-popup {{ $mode === 'kelas' ? 'is-hidden' : '' }}" id="{{ $idPopup }}"
     role="dialog" aria-modal="true" aria-labelledby="{{ $idPopup }}Judul">
    <div class="kom-lang-popup-card">
        @if ($mode === 'kelas')
            <button type="button" class="kom-lang-popup-tutup" data-tutup-popup aria-label="{{ __('Tutup') }}">&times;</button>
            <div class="kom-lang-popup-icon kelas"><i class="fas fa-graduation-cap"></i></div>
            <h4 id="{{ $idPopup }}Judul">{{ __('Pilih bahasa untuk') }} <span data-nama-kelas>-</span></h4>
            <p>{{ __('Selamat, kelas sebelumnya sudah selesai! Anda boleh memakai bahasa yang sama atau memilih bahasa lain untuk kelas ini.') }}</p>
        @else
            <div class="kom-lang-popup-icon"><i class="fas fa-language"></i></div>
            <h4 id="{{ $idPopup }}Judul">{{ __('Selamat datang di KOM EveryONE!') }}</h4>
            <p>{{ __('Sebelum mulai, pilih bahasa yang ingin Anda gunakan untuk belajar.') }}</p>
        @endif

        <form method="POST" action="{{ route('kom.bahasa.store') }}" class="kom-lang-popup-form">
            @csrf
            @if ($mode === 'kelas')
                <input type="hidden" name="kelas" value="" data-kode-kelas>
            @endif

            <div class="kom-lang-popup-list">
                @forelse ($popupBahasaList as $lang)
                    <label class="kom-lang-popup-item {{ $popupBahasaDefault === $lang->code ? 'is-selected' : '' }}">
                        <input type="radio" name="language_code" value="{{ $lang->code }}"
                               {{ $popupBahasaDefault === $lang->code ? 'checked' : '' }}>
                        <span class="kode">{{ strtoupper($lang->code) }}</span>
                        <span class="nama">
                            <strong>{{ $lang->native_name ?: $lang->name }}</strong>
                            <small>{{ $lang->name }}</small>
                        </span>
                        <span class="centang"><i class="fas fa-check-circle"></i></span>
                    </label>
                @empty
                    <div class="text-muted">{{ __('Belum ada bahasa yang tersedia. Silakan hubungi admin.') }}</div>
                @endforelse
            </div>

            @error('language_code')
                <div class="text-danger small mb-2"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</div>
            @enderror

            <button type="submit" class="kom-lang-popup-btn" {{ $popupBahasaList->isEmpty() ? 'disabled' : '' }}>
                {{ __('Lanjutkan') }} <i class="fas fa-arrow-right ml-1"></i>
            </button>
            <div class="kom-lang-popup-note">
                <i class="fas fa-info-circle mr-1"></i>{{ __('Setelah Anda memulai sebuah kelas, bahasa akan terkunci sampai kelas tersebut selesai. Pilih dengan tenang, ya.') }}
            </div>
        </form>
    </div>
</div>

@once
<style>
    body.kom-lang-popup-buka { overflow: hidden; }
    .kom-lang-popup {
        position: fixed; inset: 0; z-index: 2000; display: flex; align-items: center; justify-content: center;
        padding: 16px; background: rgba(15, 23, 42, .55);
        backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);
        animation: komLangMasuk .2s ease-out;
    }
    .kom-lang-popup.is-hidden { display: none; }
    .kom-lang-popup-card {
        position: relative; width: 100%; max-width: 460px; max-height: calc(100vh - 32px); overflow-y: auto;
        background: linear-gradient(180deg, #f2f7ff 0%, #ffffff 160px);
        border-radius: 24px; padding: 28px 24px 20px; text-align: center;
        box-shadow: 0 30px 60px -20px rgba(15, 23, 42, .5);
    }
    .kom-lang-popup-tutup {
        position: absolute; top: 12px; right: 14px; width: 34px; height: 34px; border: 0; border-radius: 50%;
        background: #eef2f8; color: #5d6a7e; font-size: 1.3rem; line-height: 1;
    }
    .kom-lang-popup-tutup:hover { background: #e1e7f0; color: #1c2b43; }
    .kom-lang-popup-icon {
        width: 60px; height: 60px; margin: 0 auto 12px; border-radius: 18px; display: flex; align-items: center; justify-content: center;
        background: linear-gradient(135deg, #2f6fed, #6a5cff); color: #fff; font-size: 1.6rem;
        box-shadow: 0 10px 22px -10px rgba(47, 111, 237, .7);
    }
    .kom-lang-popup-icon.kelas { background: linear-gradient(135deg, #22b07d, #12996a); box-shadow: 0 10px 22px -10px rgba(18, 153, 106, .7); }
    .kom-lang-popup-card h4 { font-weight: 700; color: #1c2b43; margin-bottom: 6px; }
    .kom-lang-popup-card > p { color: #7b879a; font-size: .92rem; margin-bottom: 18px; }
    .kom-lang-popup-list { display: grid; gap: 10px; margin-bottom: 16px; text-align: left; }
    .kom-lang-popup-item {
        display: flex; align-items: center; gap: 12px; margin: 0; cursor: pointer;
        padding: 12px 14px; border: 2px solid #e3e8f0; border-radius: 14px; background: #f7f9fc; transition: .15s;
    }
    .kom-lang-popup-item:hover { border-color: #9db8f2; }
    .kom-lang-popup-item input { display: none; }
    .kom-lang-popup-item .kode {
        flex: 0 0 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center;
        background: #e8eefb; color: #2a5298; font-weight: 700; font-size: .85rem;
    }
    .kom-lang-popup-item .nama { flex: 1; display: flex; flex-direction: column; line-height: 1.25; }
    .kom-lang-popup-item .nama strong { color: #1c2b43; }
    .kom-lang-popup-item .nama small { color: #8a96a8; }
    .kom-lang-popup-item .centang { color: #2f6fed; font-size: 1.2rem; opacity: 0; transition: .15s; }
    .kom-lang-popup-item.is-selected { border-color: #2f6fed; background: #f2f6ff; }
    .kom-lang-popup-item.is-selected .kode { background: #2f6fed; color: #fff; }
    .kom-lang-popup-item.is-selected .centang { opacity: 1; }
    .kom-lang-popup-btn {
        width: 100%; border: 0; border-radius: 14px; padding: 13px 18px; font-weight: 600; color: #fff;
        background: linear-gradient(135deg, #2f6fed, #5a5cf0); box-shadow: 0 12px 24px -12px rgba(47, 111, 237, .8);
    }
    .kom-lang-popup-btn:disabled { opacity: .6; }
    .kom-lang-popup-note { margin-top: 12px; font-size: .75rem; color: #9aa5b5; }
    @keyframes komLangMasuk { from { opacity: 0; transform: scale(.98); } to { opacity: 1; transform: none; } }
</style>
<script>
    (function () {
        // pilih kartu bahasa
        document.addEventListener('change', function (e) {
            var radio = e.target;
            if (!radio.matches || !radio.matches('.kom-lang-popup-item input')) return;
            var popup = radio.closest('.kom-lang-popup');
            popup.querySelectorAll('.kom-lang-popup-item').forEach(function (el) { el.classList.remove('is-selected'); });
            radio.closest('.kom-lang-popup-item').classList.add('is-selected');
        });
        // cegah klik ganda saat menyimpan
        document.addEventListener('submit', function (e) {
            if (!e.target.matches || !e.target.matches('.kom-lang-popup-form')) return;
            var btn = e.target.querySelector('button[type=submit]');
            if (btn) btn.disabled = true;
        });
        // tutup (hanya pop-up mode kelas yang punya tombol tutup)
        document.addEventListener('click', function (e) {
            var tutup = e.target.closest && e.target.closest('[data-tutup-popup]');
            var popup = tutup ? tutup.closest('.kom-lang-popup') : null;
            if (!popup && e.target.classList && e.target.classList.contains('kom-lang-popup') && e.target.id === 'komLangPopupKelas') {
                popup = e.target; // klik di luar kartu
            }
            if (popup) {
                popup.classList.add('is-hidden');
                document.body.classList.remove('kom-lang-popup-buka');
            }
        });
        // pop-up user baru langsung tampil & mengunci halaman
        if (document.getElementById('komLangPopupBaru')) document.body.classList.add('kom-lang-popup-buka');

        // dipanggil tombol "Mulai Kelas" di katalog
        window.komBukaPopupBahasa = function (kode, nama) {
            var popup = document.getElementById('komLangPopupKelas');
            if (!popup) return false;
            popup.querySelector('[data-kode-kelas]').value = kode;
            popup.querySelector('[data-nama-kelas]').textContent = nama;
            popup.classList.remove('is-hidden');
            document.body.classList.add('kom-lang-popup-buka');
            return true;
        };
    })();
</script>
@endonce
