{{--
    kom_v02: tombol hapus konten salinan / hapus bahasa.
    Variabel: $lang, $info (hasil HapusKontenBahasaService::ringkasan), $kelas (opsional), $mode ('konten'|'bahasa')
--}}
@php
    $kelas = $kelas ?? null;
    $k = $info['konten'];
    $judul = $mode === 'bahasa'
        ? __('Hapus bahasa :bahasa sampai bersih?', ['bahasa' => $lang->name])
        : __('Hapus konten :bahasa untuk kelas :kelas?', ['bahasa' => $lang->name, 'kelas' => $kelas?->translatedName('id')]);
    $rincian = __(':modul modul, :video video, :soal pertanyaan, :jawaban jawaban akan dihapus.', $k);
    if ($mode === 'bahasa') {
        $rincian .= ' ' . __('Nama kelas, terjemahan tampilan, dan data bahasanya juga dihapus.');
    }
    $rincian .= ' ' . __('Konten Bahasa Indonesia tidak tersentuh. Tindakan ini tidak bisa dibatalkan.');
    $penghalang = collect($info['penghalang'])->map(fn ($n, $x) => "$x ($n)")->implode(', ');
@endphp

@if (! $info['boleh'])
    <button type="button" class="btn btn-outline-secondary btn-sm" disabled
        title="{{ __('Tidak bisa dihapus karena masih ada: :daftar.', ['daftar' => $penghalang]) }}">
        <i class="fas fa-lock mr-1"></i>{{ $mode === 'bahasa' ? __('Hapus Bahasa') : __('Hapus') }}
    </button>
@else
    <form method="POST" class="d-inline m-0 kom-form-hapus"
        action="{{ $mode === 'bahasa' ? route('kom.super_bahasa.destroy', $lang) : route('kom.super_bahasa.hapus_konten', $lang) }}"
        data-kode="{{ strtoupper($lang->code) }}"
        data-pesan="{{ $judul }}&#10;&#10;{{ $rincian }}">
        @csrf
        @if ($mode === 'bahasa') @method('DELETE') @endif
        @if ($kelas) <input type="hidden" name="class_id" value="{{ $kelas->id }}"> @endif
        <input type="hidden" name="konfirmasi" value="">

        @if ($info['ada_peserta'])
            {{-- Ada data belajar peserta → wajib dicentang supaya ikut terhapus --}}
            <label class="small text-danger mb-0 mr-1" style="white-space:normal"
                title="{{ collect($info['peserta'])->map(fn ($n, $x) => "$x: $n")->implode(' · ') }}">
                <input type="checkbox" name="termasuk_peserta" value="1" class="mr-1" required>
                {{ __('hapus juga data peserta') }}
            </label>
        @endif

        <button class="btn btn-outline-danger btn-sm">
            <i class="fas fa-trash-alt mr-1"></i>{{ $mode === 'bahasa' ? __('Hapus Bahasa') : __('Hapus') }}
        </button>
    </form>
@endif

@once
    <script>
        document.addEventListener('submit', function (e) {
            var form = e.target;
            if (!form.classList || !form.classList.contains('kom-form-hapus')) return;
            var kode = form.dataset.kode;
            var ketik = window.prompt(form.dataset.pesan + '\n\n' + @json(__('Ketik kode bahasa')) + ' ' + kode + ' ' + @json(__('untuk melanjutkan:')));
            if (!ketik || ketik.trim().toUpperCase() !== kode) {
                e.preventDefault();
                if (ketik !== null) alert(@json(__('Konfirmasi tidak cocok. Data tidak dihapus.')));
                return;
            }
            form.querySelector('input[name="konfirmasi"]').value = ketik.trim();
        });
    </script>
@endonce
