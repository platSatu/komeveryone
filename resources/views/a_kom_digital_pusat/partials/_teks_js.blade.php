{{--
    kom_v02: teks tampilan untuk JavaScript (pop-up, tombol, pesan) mengikuti bahasa peserta.

    Cara pakai di halaman:
        @php
            $komTeksJs = [
                'Teks Indonesia' => __ ('Teks Indonesia'),   // (tanpa spasi setelah __) tulis apa adanya
            ];                                               // supaya terdeteksi "php artisan kom:pindai-teks"
        @endphp
        @include('a_kom_digital_pusat.partials._teks_js', ['teks' => $komTeksJs])

    Di JavaScript:  komT('Teks Indonesia')  atau  komT('Modul :nomor', {nomor: 2})
--}}
<script>
    window.komTeks = Object.assign(window.komTeks || {}, @json($teks ?? []));
    window.komT = window.komT || function (teks, ganti) {
        var hasil = (window.komTeks && window.komTeks[teks]) || teks;
        if (ganti) {
            Object.keys(ganti).forEach(function (k) { hasil = hasil.split(':' + k).join(ganti[k]); });
        }
        return hasil;
    };
</script>
