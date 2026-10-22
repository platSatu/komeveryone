{{-- kom_v02: tampilkan nilai 0–5 sebagai bintang. Pakai: @include('...._bintang', ['nilai' => 4.3]) --}}
@php
    $n = (float) ($nilai ?? 0);
    $penuh = (int) floor($n);
    $setengah = ($n - $penuh) >= 0.5 ? 1 : 0;
@endphp
@if ($n > 0)
    <span class="kom-rv-bintang" title="{{ number_format($n, 2) }} / 5">
        @for ($i = 0; $i < $penuh; $i++)<i class="fas fa-star"></i>@endfor
        @if ($setengah)<i class="fas fa-star-half-alt"></i>@endif
        @for ($i = 0; $i < 5 - $penuh - $setengah; $i++)<i class="far fa-star kosong"></i>@endfor
        <small class="text-muted ml-1">{{ number_format($n, 1) }}</small>
    </span>
@else
    <span class="text-muted">-</span>
@endif
