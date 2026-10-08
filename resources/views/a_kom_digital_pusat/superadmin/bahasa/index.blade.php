@extends('layouts.KomDigital')

@section('page_title', __('Kelola Bahasa'))

{{-- kom_v02: Superadmin — Kelola Bahasa --}}
@section('content')
<div class="kom-page container-fluid px-0">
    @include('a_kom_digital_pusat.katalog._flash')
    @if ($errors->any())
        <div class="kom-note kom-note-danger mb-3">@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
    @endif

    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3" style="gap:10px">
        <p class="kom-text mb-0">{{ __('Tambahkan bahasa, salin konten kelas, terjemahkan, lalu aktifkan agar bisa dipilih peserta.') }}</p>
        <a href="{{ route('kom.super_bahasa.create') }}" class="btn btn-primary kom-btn"><i class="fas fa-plus mr-1"></i>{{ __('Tambah Bahasa') }}</a>
    </div>

    <div class="row g-3">
        @foreach ($languages as $lang)
            <div class="col-12">
                <div class="kom-card">
                    <div class="d-flex justify-content-between align-items-start flex-wrap" style="gap:12px">
                        <div class="d-flex align-items-center" style="gap:14px">
                            <span class="kom-lang-code">{{ strtoupper($lang->code) }}</span>
                            <div>
                                <h5 class="mb-0 font-weight-bold">{{ $lang->native_name ?: $lang->name }}
                                    @if ($lang->is_default) <span class="kom-pill ml-1">{{ __('Bahasa Utama') }}</span> @endif
                                </h5>
                                <small class="kom-text">{{ $lang->name }}
                                    @if ($lang->certificate_prefix) · {{ __('Prefix sertifikat:') }} {{ $lang->certificate_prefix }} @endif
                                </small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center flex-wrap" style="gap:8px">
                            <span class="kom-badge {{ $lang->is_active ? 'kom-badge-selesai' : 'kom-badge-segera' }}">
                                {{ $lang->is_active ? __('Aktif') : __('Nonaktif') }}
                            </span>
                            <a href="{{ route('kom.super_bahasa.edit', $lang) }}" class="btn btn-light btn-sm">{{ __('Ubah') }}</a>
                            @unless ($lang->is_default)
                                <form method="POST" action="{{ route('kom.super_bahasa.toggle', $lang) }}" class="m-0"
                                      onsubmit="return confirm('{{ $lang->is_active ? __('Nonaktifkan bahasa ini? Peserta baru tidak bisa memilihnya lagi.') : __('Aktifkan bahasa ini? Pastikan konten sudah lengkap.') }}')">
                                    @csrf
                                    <button class="btn btn-sm {{ $lang->is_active ? 'btn-outline-secondary' : 'btn-success' }}">
                                        {{ $lang->is_active ? __('Nonaktifkan') : __('Aktifkan') }}
                                    </button>
                                </form>
                            @endunless
                        </div>
                    </div>

                    <div class="table-responsive mt-3">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr class="kom-text">
                                    <th>{{ __('Kelas') }}</th>
                                    <th class="text-center">{{ __('Modul') }}</th>
                                    <th class="text-center">{{ __('Video') }}</th>
                                    <th class="text-center">{{ __('Pertanyaan') }}</th>
                                    <th>{{ __('Kelengkapan') }}</th>
                                    <th class="text-right">{{ __('Aksi') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($classes as $kelas)
                                    @php $r = $ringkasan[$lang->code][$kelas->id]; @endphp
                                    <tr>
                                        <td class="font-weight-bold">{{ $kelas->translatedName($lang->code) }}</td>
                                        <td class="text-center">{{ $r['modul'] }}</td>
                                        <td class="text-center">{{ $r['video'] }}</td>
                                        <td class="text-center">{{ $r['soal'] }}</td>
                                        <td>
                                            @if ($r['siap'])
                                                <span class="text-success"><i class="fas fa-check-circle mr-1"></i>{{ __('Siap') }}</span>
                                            @elseif ($r['modul'] === 0)
                                                <span class="kom-text">{{ __('Belum ada konten') }}</span>
                                            @else
                                                <ul class="mb-0 pl-3 small text-warning">
                                                    @foreach ($r['masalah'] as $m) <li>{{ $m }}</li> @endforeach
                                                </ul>
                                            @endif
                                        </td>
                                        <td class="text-right" style="white-space:nowrap">
                                            @if ($r['modul'] === 0 && ! $lang->is_default)
                                                <form method="POST" action="{{ route('kom.super_bahasa.salin') }}" class="d-inline-flex m-0" style="gap:6px"
                                                      onsubmit="return confirm('{{ __('Salin seluruh modul, video, pertanyaan, dan jawaban kelas ini ke bahasa :bahasa?', ['bahasa' => $lang->name]) }}')">
                                                    @csrf
                                                    <input type="hidden" name="class_id" value="{{ $kelas->id }}">
                                                    <input type="hidden" name="ke" value="{{ $lang->code }}">
                                                    <select name="dari" class="form-control form-control-sm" style="width:auto">
                                                        @foreach ($languages->where('code', '!=', $lang->code) as $asal)
                                                            <option value="{{ $asal->code }}">{{ __('dari') }} {{ strtoupper($asal->code) }}</option>
                                                        @endforeach
                                                    </select>
                                                    <button class="btn btn-primary btn-sm"><i class="fas fa-copy mr-1"></i>{{ __('Salin Konten') }}</button>
                                                </form>
                                            @elseif ($r['modul'] > 0)
                                                <form method="POST" action="{{ route('kom.super_bahasa.kelola') }}" class="d-inline m-0">
                                                    @csrf
                                                    <input type="hidden" name="language_code" value="{{ $lang->code }}">
                                                    <input type="hidden" name="lanjut" value="modul">
                                                    <button class="btn btn-light btn-sm"><i class="fas fa-edit mr-1"></i>{{ __('Kelola Konten') }}</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="kom-note mt-4">
        <strong>{{ __('Langkah menambah bahasa baru:') }}</strong>
        <ol class="mb-0 pl-3 mt-1">
            <li>{{ __('Tambah Bahasa (mis. kode zh, nama 中文).') }}</li>
            <li>{{ __('Klik Salin Konten pada kelas yang ingin disediakan.') }}</li>
            <li>{{ __('Terjemahkan judul modul, video, pertanyaan, dan jawaban, lalu unggah file video bahasa tersebut.') }}</li>
            <li>{{ __('Isi Terjemahan Tampilan (menu & tombol) dan nama kelas di Kelola Kelas.') }}</li>
            <li>{{ __('Setelah kelengkapan "Siap", klik Aktifkan.') }}</li>
        </ol>
    </div>
</div>
@include('a_kom_digital_pusat.katalog._style')
@endsection
