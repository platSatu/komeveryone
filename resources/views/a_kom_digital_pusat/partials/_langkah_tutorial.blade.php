{{--
    kom_v02: penunjuk langkah rangkaian kelas tutorial (Jadwal → Pertanyaan → Persembahan).
    Pemakaian: @include('a_kom_digital_pusat.partials._langkah_tutorial', ['aktif' => 1|2|3])
--}}
@php
    $aktif = (int) ($aktif ?? 1);
    $langkahTutorial = [1 => __('Jadwal'), 2 => __('Pertanyaan'), 3 => __('Persembahan')];
@endphp
<ol class="kom-steps">
    @foreach ($langkahTutorial as $no => $namaLangkah)
        <li class="{{ $no < $aktif ? 'selesai' : ($no === $aktif ? 'aktif' : '') }}">
            <span>@if ($no < $aktif)<i class="fas fa-check"></i>@else{{ $no }}@endif</span>{{ $namaLangkah }}
        </li>
    @endforeach
</ol>
@once
    <style>
        .kom-steps { display:flex; justify-content:space-between; list-style:none; padding:0; margin:0 auto 28px; max-width:420px; position:relative; }
        .kom-steps::before { content:''; position:absolute; top:13px; left:16%; right:16%; height:2px; background:#dfe7f3; }
        .kom-steps li { position:relative; flex:1; text-align:center; font-size:.78rem; color:#8a96a8; }
        .kom-steps li span {
            display:flex; align-items:center; justify-content:center; width:28px; height:28px; margin:0 auto 6px;
            border-radius:50%; background:#fff; border:2px solid #dfe7f3; font-size:.75rem; font-weight:600;
        }
        .kom-steps li.selesai span { background:#22b07d; border-color:#22b07d; color:#fff; }
        .kom-steps li.aktif { color:#1f3b63; font-weight:600; }
        .kom-steps li.aktif span { background:#2f6fed; border-color:#2f6fed; color:#fff; box-shadow:0 0 0 5px rgba(47,111,237,.15); }
    </style>
@endonce
