@extends('layouts.KomDigital')

@section('content')
    <div class="card">
        <div class="card-header">
            <h5>
                Pertanyaan Peserta -
                {{ $kelasTutorial->kelas->name ?? 'Kelas' }} /
                {{ $kelasTutorial->modul->name ?? 'Modul' }}
            </h5>
        </div>
        <div class="card-body">
            @if ($pertanyaans->isEmpty())
                <p class="text-muted">Belum ada pertanyaan yang dikirimkan peserta.</p>
            @else
                @php
                    // Kelompokkan pertanyaan berdasarkan user_id
                    $pertanyaanPerUser = $pertanyaans->groupBy('user_id');
                @endphp

                @foreach ($pertanyaanPerUser as $userId => $pertanyaanUser)
                    <div class="mb-4">
                        <strong>{{ $loop->iteration }}. {{ $pertanyaanUser->first()->user->name ?? 'Peserta' }}</strong>
                        <ul>
                            @foreach ($pertanyaanUser as $pertanyaan)
                                <li>
                                    {{ $pertanyaan->pertanyaan }}
                                    <br>
                                    <small class="text-muted">Dikirim:
                                        {{ $pertanyaan->tgl_submit?->format('d M Y H:i') }}</small>
                                </li>
                            @endforeach
                        </ul>
                        <hr>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
@endsection
