@extends('layouts.kom')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card mb-3">
                <div class="card-header">
                    <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($jadwal->datetime)->format('d M Y H:i') }} <br>
                    <strong>Deskripsi:</strong> {{ $jadwal->description }}<br>
                    <strong>Pengajar :</strong> {{ $jadwal->pengajar->username ?? '-' }}
                </div>
                <div class="card-body">
                    <h5>Peserta:</h5>

                    @forelse($jadwal->kelasTutorial as $kelas)
                        <div class="mb-3 border p-2 rounded">
                            <strong>Nama:</strong> {{ $kelas->user->username ?? '-' }} <br>

                            @php
                                $pertanyaans = $kelas->pertanyaan
                                    ->where('kom_schedules_id', $jadwal->id)
                                    ->groupBy(function ($item) {
                                        return \Carbon\Carbon::parse($item->tgl_pertanyaan)->format('d M Y');
                                    });
                            @endphp

                            @if ($pertanyaans->isNotEmpty())
                                <strong>Pertanyaan:</strong><br>
                                @foreach ($pertanyaans as $tanggal => $listPertanyaan)
                                    
                                    <ul>
                                        @foreach ($listPertanyaan as $pertanyaan)
                                            <li>{{ $pertanyaan->pertanyaan }}</li>
                                        @endforeach
                                    </ul>
                                @endforeach
                            @else
                                <em>Tidak ada pertanyaan</em>
                            @endif
                        </div>
                    @empty
                        <p><em>Tidak ada peserta pada jadwal ini.</em></p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
