{{-- kom_v02: tabel jadwal kelas tutorial — modern & minimalis --}}
@if($kelasTutorials->isEmpty())
    <div class="kom-kt-kosong">
        <div class="kom-kt-kosong-icon"><i class="far fa-calendar-times"></i></div>
        <div>{{ __('Tidak ada kelas tutorial ditemukan.') }}</div>
    </div>
@else
<div class="kom-kt-table-wrap">
    <table class="table kom-kt-table mb-0">
        <thead>
            <tr>
                <th>#</th>
                <th>{{ __('Modul') }}</th>
                <th>{{ __('Kelas') }}</th>
                <th>{{ __('Rayon') }}</th>
                <th>{{ __('Waktu Pelaksanaan Kelas') }}</th>
                <th class="text-center">{{ __('Kuota Tersisa') }}</th>
                <th class="text-right">{{ __('Ikut Kelas') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kelasTutorials as $index => $kelas)
                <tr>
                    <td class="text-muted">{{ $index + 1 }}</td>
                    <td>
                        <div class="font-weight-bold">{{ $kelas->modul->name ?? '-' }}</div>
                        <span class="kom-kt-chip"><i class="fas fa-flag-checkered mr-1"></i>{{ $kelas->label_titik }}</span>
                    </td>
                    <td>{{ $kelas->kelas->name ?? '-' }}</td>
                    <td>{{ $kelas->rayon->name ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($kelas->start_time)->translatedFormat('l, d F Y H:i') }}</td>
                    <td class="text-center">
                        @if($kelas->sisa_quota > 0)
                            <span class="kom-kt-chip biru">{{ $kelas->sisa_quota }}</span>
                        @else
                            <span class="kom-kt-chip merah">{{ __('Penuh') }}</span>
                        @endif
                    </td>
                    <td class="text-right">
                        @if($kelas->sisa_quota > 0)
                            <form action="{{ route('kom.kelas_tutorial.join') }}" method="POST" class="joinClassForm d-inline">
                                @csrf
                                <input type="hidden" name="class_tutorial_id" value="{{ $kelas->id }}">
                                <button type="submit" class="kom-btn-primary kecil">
                                    <i class="fas fa-sign-in-alt"></i> {{ __('Ikut Kelas') }}
                                </button>
                            </form>
                        @else
                            <button class="kom-btn-penuh kecil" disabled title="{{ __('Kuota kelas sudah penuh') }}">
                                <i class="fas fa-times-circle"></i> {{ __('Kuota Penuh') }}
                            </button>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
@once
    <style>
        .kom-kt-table-wrap { border:1px solid #e9eef6; border-radius:18px; overflow:auto; background:#fff; }
        .kom-kt-table thead th { background:#f7f9fc; border:0; color:#7b879a; font-size:.75rem; font-weight:600;
            text-transform:uppercase; letter-spacing:.4px; padding:14px 16px; white-space:nowrap; }
        .kom-kt-table td { border-top:1px solid #f0f3f8; padding:14px 16px; vertical-align:middle; color:#33415a; font-size:.9rem; }
        .kom-kt-table tbody tr:hover { background:#fafcff; }
        .kom-kt-chip { display:inline-block; font-size:.72rem; font-weight:600; padding:3px 9px; border-radius:99px; background:#eef2f8; color:#5d6a7e; margin-top:4px; }
        .kom-kt-chip.biru { background:#eaf2ff; color:#2f6fed; }
        .kom-kt-chip.merah { background:#fdecec; color:#b42318; }
        .kom-kt-kosong { text-align:center; padding:40px 16px; color:#7b879a; background:#f7f9fc; border-radius:18px; }
        .kom-kt-kosong-icon { font-size:2rem; color:#b6c2d4; margin-bottom:8px; }
    </style>
@endonce
