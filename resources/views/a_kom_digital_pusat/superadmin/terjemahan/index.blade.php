@extends('layouts.KomDigital')

@section('page_title', __('Terjemahan Tampilan'))

{{-- kom_v02: Superadmin — Terjemahan Tampilan (menu, tombol, judul, pesan) --}}
@section('content')
<div class="kom-page container-fluid px-0">
    @include('a_kom_digital_pusat.katalog._flash')

    @if ($bahasaList->isEmpty())
        <div class="kom-note">{{ __('Belum ada bahasa selain Bahasa Indonesia. Tambahkan bahasa di menu Kelola Bahasa.') }}
            <a href="{{ route('kom.super_bahasa.index') }}">{{ __('Kelola Bahasa') }}</a></div>
    @else
        <div class="kom-card mb-3">
            <form method="GET" class="d-flex flex-wrap align-items-end" style="gap:12px">
                <div>
                    <label class="small font-weight-bold mb-1">{{ __('Bahasa') }}</label>
                    <select name="bahasa" class="form-control" onchange="this.form.submit()">
                        @foreach ($bahasaList as $l)
                            <option value="{{ $l->code }}" {{ $l->code === $bahasa ? 'selected' : '' }}>{{ strtoupper($l->code) }} — {{ $l->native_name ?: $l->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-grow-1">
                    <label class="small font-weight-bold mb-1">{{ __('Cari teks') }}</label>
                    <input type="text" name="cari" value="{{ request('cari') }}" class="form-control" placeholder="{{ __('mis. Pilih Kelas') }}">
                </div>
                <label class="mb-2"><input type="checkbox" name="kosong" value="1" {{ request('kosong') ? 'checked' : '' }}> {{ __('Hanya yang belum diisi') }}</label>
                <button class="btn btn-light">{{ __('Tampilkan') }}</button>
            </form>
            <div class="d-flex justify-content-between align-items-center flex-wrap mt-3" style="gap:10px">
                <div class="flex-grow-1" style="max-width:420px">
                    <div class="d-flex justify-content-between small kom-text mb-1">
                        <span>{{ __(':terisi dari :total teks sudah diterjemahkan', ['terisi' => $terisi, 'total' => $total]) }}</span>
                        <strong>{{ $total ? round($terisi / $total * 100) : 0 }}%</strong>
                    </div>
                    <div class="kom-progress"><div style="width: {{ $total ? round($terisi / $total * 100) : 0 }}%"></div></div>
                </div>
                <form method="POST" action="{{ route('kom.super_terjemahan.pindai') }}" class="m-0">
                    @csrf
                    <button class="btn btn-outline-primary btn-sm"><i class="fas fa-sync mr-1"></i>{{ __('Pindai Teks Baru') }}</button>
                </form>
            </div>
        </div>

        <form method="POST" action="{{ route('kom.super_terjemahan.simpan') }}">
            @csrf
            <input type="hidden" name="bahasa" value="{{ $bahasa }}">
            <div class="kom-card p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr class="kom-text">
                                <th style="width:45%">{{ __('Teks Bahasa Indonesia') }}</th>
                                <th>{{ __('Terjemahan') }} ({{ strtoupper($bahasa) }})</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($rows as $row)
                                <tr>
                                    <td class="align-middle">{{ $row->key }}</td>
                                    <td>
                                        <textarea name="values[{{ $row->id }}]" rows="{{ mb_strlen($row->key) > 80 ? 3 : 1 }}"
                                                  class="form-control {{ $row->value ? '' : 'border-warning' }}">{{ $row->value }}</textarea>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="text-center kom-text py-4">{{ __('Tidak ada teks. Klik "Pindai Teks Baru" untuk mengambil teks dari aplikasi.') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($rows instanceof \Illuminate\Pagination\AbstractPaginator && $rows->count())
                <div class="d-flex justify-content-between align-items-center flex-wrap mt-3" style="gap:10px">
                    <div>{{ $rows->links() }}</div>
                    <button class="btn btn-primary kom-btn"><i class="fas fa-save mr-1"></i>{{ __('Simpan Halaman Ini') }}</button>
                </div>
                <small class="kom-text d-block mt-2">{{ __('Teks yang mengandung :nama, :kelas, dsb. adalah bagian yang otomatis diisi sistem — biarkan tetap ada di terjemahan.') }}</small>
            @endif
        </form>
    @endif
</div>
@include('a_kom_digital_pusat.katalog._style')
@endsection
