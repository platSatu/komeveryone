@extends('layouts.kom')

@section('content')
<div class="container my-4">
    <div class="card p-4">

        {{-- Notifikasi --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
<h2 class="mb-4">Kelas Tutorial</h2>

        {{-- Form pencarian & filter --}}
       <form method="GET" action="{{ route('kom.kelas_tutor.index') }}" class="mb-4">
    <div class="row g-2 align-items-stretch">
        <div class="col-md-5">
            <div class="h-100">
                <input type="text" name="search" class="form-control h-100" placeholder="Cari kelas, rayon, modul..." value="{{ old('search', $keyword) }}">
            </div>
        </div>
        <div class="col-md-4">
    <div class="h-100">
        <select name="rayon" class="form-control h-100">
            <option value="">-- Filter Rayon --</option>
            @foreach($rayons as $rayon)
                <option value="{{ $rayon->id }}" {{ $rayonFilter == $rayon->id ? 'selected' : '' }}>
                    {{ $rayon->name }}
                </option>
            @endforeach
        </select>
    </div>
</div>

        <div class="col-md-3">
            <button type="submit" class="btn btn-primary w-100 h-100">Cari & Filter</button>
        </div>
    </div>
</form>


        {{-- List Jadwal --}}
        <div class="row g-4">
            @forelse($schedules as $schedule)
                <div class="col-md-3">
                    <div class="card h-100 shadow-sm">
                        <img src="{{ asset('home/kom/banner.jpg') }}" class="card-img-top" alt="KOM 100">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $schedule->rayon->name ?? '-' }}</h5>
                            <ul class="list-unstyled flex-grow-1">
                                <li><strong>Kelas:</strong> {{ $schedule->komClass->name ?? '-' }}</li>
                                <li><strong>Modul:</strong> {{ $schedule->komModul->title ?? '-' }}</li>
                                <li><strong>Jadwal:</strong> {{ \Carbon\Carbon::parse($schedule->datetime)->format('d M Y H:i') }}</li>
                                <li><strong>Kuota:</strong> {{ $schedule->quota }}</li>
                                <li><strong>Peserta Terdaftar:</strong> {{ $schedule->kelas_tutorial_count }}</li>
                                <li><strong>Sisa Kuota:</strong> {{ $schedule->quota - $schedule->kelas_tutorial_count }}</li>
                            </ul>

                            <p class="card-text">{{ Str::limit($schedule->description, 100) }}</p>

                            @if ($sudahBooking && !$punyaKelasNonaktif)
                                <span class="badge bg-warning mt-auto align-self-start">Anda sudah memilih kelas</span>
                            @elseif ($punyaKelasNonaktif)
                                <button type="button" class="btn btn-info mt-auto" data-bs-toggle="modal"
                                    data-bs-target="#confirmBookingModal_{{ $schedule->id }}">
                                    Pilih Ulang Kelas
                                </button>
                            @elseif ($schedule->kelas_tutorial_count < $schedule->quota)
                                <button type="button" class="btn btn-primary mt-auto" data-bs-toggle="modal"
                                    data-bs-target="#confirmBookingModal_{{ $schedule->id }}">
                                    Booking Kelas
                                </button>
                            @else
                                <span class="badge bg-danger mt-auto align-self-start">Kuota Penuh</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Modal Konfirmasi --}}
                <div class="modal fade" id="confirmBookingModal_{{ $schedule->id }}" tabindex="-1"
                    aria-labelledby="confirmBookingLabel_{{ $schedule->id }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="confirmBookingLabel_{{ $schedule->id }}">Konfirmasi Booking</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                            </div>
                            <div class="modal-body">
                                Yakin Anda ingin memilih kelas ini?
                            </div>
                            <div class="modal-footer">
                                <form action="{{ $punyaKelasNonaktif ? route('kom.update_rayon') : route('kom.kelas_tutorial.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="kom_schedules_id" value="{{ $schedule->id }}">
                                    <input type="hidden" name="kom_moduls_id" value="{{ $schedule->kom_modul_id }}">
                                    <input type="hidden" name="kom_class_id" value="{{ $schedule->kom_class_id }}">
                                    <input type="hidden" name="rayons_id" value="{{ $schedule->rayon_id }}">
                                    <input type="hidden" name="tgl_booking" value="{{ \Carbon\Carbon::now() }}">

                                    <button type="submit" class="btn btn-success">
                                        Yakin, Pilih Kelas Ini
                                    </button>
                                </form>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-warning text-center">
                        <strong>Data jadwal tidak tersedia.</strong><br>
                        Silakan coba kembali nanti atau gunakan filter lain.
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="pagination justify-content-center mt-4">
            {{ $schedules->links('pagination::bootstrap-4') }}
        </div>
       

    </div>
</div>
@endsection
