@extends('layouts.KomDigital')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">

                    <form action="{{ route('kom.inputMassal.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            {{-- Pilih User --}}
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="user_id" class="form-label">Pilih User</label>
                                    <select name="user_id" id="user_id" class="form-select form-control" required>
                                        <option value="">-- Pilih User --</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name ?? $user->email }} | {{ $user->email }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- Pilih Kelas Tutorial --}}
                            <div class="col-md-5">
                                <div class="mb-3">
                                    <label for="kelas_tutorial" class="form-label">Pilih Kelas Tutorial</label>
                                    <select name="kelas_tutorial[]" id="kelas_tutorial" class="form-select" multiple
                                        size="5">
                                        @foreach ($KelasTutorial as $t)
                                            <option value="{{ $t->id }}">
                                                {{ $t->topic ?? 'Tutorial ' . $t->id }} |
                                                {{ \Carbon\Carbon::parse($t->start_time)->format('d M Y H:i') }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">Tekan Ctrl (Cmd) + klik untuk memilih lebih dari
                                        satu</small>
                                </div>
                            </div>

                            {{-- Tanggal Booking --}}
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="date_booking" class="form-label">Tanggal Booking</label>
                                    <input type="date" name="date_booking" id="date_booking" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <h5>Pilih Video dan Statusnya</h5>
                                @foreach ($videos as $video)
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="form-check me-3 col-md-6">
                                            <input type="checkbox" name="videos[{{ $video->id }}][checked]"
                                                id="video_{{ $video->id }}" class="form-check-input" value="1">
                                            <label for="video_{{ $video->id }}" class="form-check-label">
                                                {{ $video->title ?? 'Video ' . $video->name }}
                                            </label>
                                        </div>
                                        <div class="col-md-6">
                                            <select name="videos[{{ $video->id }}][status]"
                                                class="form-select form-select-sm form-control" style="width: 150px;">
                                                <option value="unlocked">unlocked</option>
                                                <option value="in_progress">In Progress</option>
                                                <option value="completed">Completed</option>
                                            </select>
                                        </div>
                                    </div>
                                @endforeach
                                <small class="form-text text-muted">Centang video yang ingin dipilih, dan pilih
                                    statusnya.</small>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <label for="pertanyaan1" class="form-label">Pertanyaan 1 (optional)</label>
                                <textarea name="pertanyaan[0]" id="pertanyaan1" class="form-control" rows="3"
                                    placeholder="Tulis pertanyaan pertama..."></textarea>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <label for="pertanyaan2" class="form-label">Pertanyaan 2 (optional)</label>
                                <textarea name="pertanyaan[1]" id="pertanyaan2" class="form-control" rows="3"
                                    placeholder="Tulis pertanyaan kedua..."></textarea>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h5>Pilih Modul dan Statusnya</h5>
                                @foreach ($moduls as $modul)
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="form-check me-3 col-md-6">
                                            <input type="checkbox" name="moduls[{{ $modul->id }}][checked]"
                                                id="modul_{{ $modul->id }}" class="form-check-input" value="1">
                                            <label for="modul_{{ $modul->id }}" class="form-check-label">
                                                {{ $modul->name ?? 'Modul ' . $modul->id }}
                                            </label>
                                        </div>
                                        <div class="col-md-6">
                                            <select name="moduls[{{ $modul->id }}][status]"
                                                class="form-select form-select-sm form-control" style="width: 150px;">
                                                <option value="locked" selected>locked</option>
                                                <option value="unlocked">unlocked</option>
                                                <option value="completed">Completed</option>
                                            </select>
                                        </div>
                                    </div>
                                @endforeach
                                <small class="form-text text-muted">Centang modul yang ingin dipilih, dan pilih
                                    statusnya.</small>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h5>Pertanyaan dan Jawaban Benar</h5>
                            @foreach ($questions as $question)
                                <div class="mb-3">
                                    <label><strong>{{ $question->questions }}</strong></label>
                                    @foreach ($question->answers as $answer)
                                        <div class="form-check">
                                            <input type="checkbox" name="user_answers[{{ $question->id }}][]"
                                                value="{{ $answer->id }}" class="form-check-input"
                                                id="answer_{{ $answer->id }}">
                                            <label class="form-check-label"
                                                for="answer_{{ $answer->id }}">{{ $answer->answer }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>





                        {{-- Submit Button --}}
                        <button type="submit" class="btn btn-primary">Simpan Data</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Sukses',
                text: "{{ session('success') }}",
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false,
            });
        @endif

        @if ($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan',
                html: `
                <ul style="text-align: left;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            `,
                confirmButtonText: 'Tutup'
            });
        @endif
    </script>
@endsection
