{{--
    kom_v02: penanda "Kelas Tutorial" di bawah video yang menjadi titik tutorial.
    Tidak mengunci video berikutnya — peserta boleh lanjut menonton,
    tetapi sertifikat hanya terbit jika hadir di semua kelas tutorial.
--}}
@php
    $status = $titik['status'];
    $info = [
        'belum' => ['secondary', __('Belum ikut')],
        'proses' => ['warning', __('Belum selesai')],
        'terdaftar' => ['primary', __('Terdaftar')],
        'tidak_hadir' => ['danger', __('Tidak hadir')],
        'hadir' => ['success', __('Hadir')],
    ][$status] ?? ['secondary', $status];
    $tombol = [
        'belum' => __('Pilih Jadwal'),
        'proses' => __('Lanjutkan'),
        'tidak_hadir' => __('Pilih Jadwal Pengganti'),
    ][$status] ?? null;
@endphp
<div class="kom-titik-tutorial d-flex align-items-center flex-wrap px-3 py-2">
    <i class="fas fa-chalkboard-teacher text-info mr-2"></i>
    <small class="font-weight-bold text-dark mr-2">{{ __('Kelas Tutorial') }}</small>
    <small class="text-muted mr-2">· {{ $titik['label'] }}</small>
    <span class="badge badge-{{ $info[0] }} mr-2">{{ $info[1] }}</span>
    @if ($tombol && $videoSelesai)
        <a href="{{ $titik['url'] }}" class="btn btn-sm btn-outline-info ml-auto py-0">{{ $tombol }}</a>
    @endif
</div>
@once
    <style>
        .kom-titik-tutorial { background: #f1f9fb; border-top: 1px dashed #b8e1ea; }
    </style>
@endonce
