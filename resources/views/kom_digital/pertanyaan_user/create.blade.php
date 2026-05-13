@extends('layouts.kom')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h2>Tanya Jawab Kelas: {{ $schedule->komClass->name ?? 'Nama Kelas' }}</h2>

                    {{-- Menampilkan pesan sukses --}}
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- Menampilkan pesan error --}}
                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Form Pertanyaan -->
                    <form id="pertanyaanForm" action="{{ route('kom.pertanyaan_peserta.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="kom_schedules_id" value="{{ $schedule->id }}">
                        <input type="hidden" name="kom_class_id" value="{{ $schedule->kom_class_id }}">
                        <input type="hidden" name="kom_moduls_id" value="{{ $schedule->kom_modul_id }}">
                        <input type="hidden" name="rayons_id" value="{{ $schedule->rayon_id }}">

                        <!-- Menambahkan inputan untuk tiga pertanyaan -->
                        @for ($i = 1; $i <= 3; $i++)
                            <div class="form-group">
                                <label for="pertanyaan_{{ $i }}">Pertanyaan {{ $i }}</label>
                                <textarea class="form-control" name="pertanyaan_{{ $i }}" id="pertanyaan_{{ $i }}"
                                    rows="3" required>{{ old('pertanyaan_' . $i) }}</textarea>
                                @error('pertanyaan_' . $i)
                                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        @endfor

                        <button type="button" id="submitBtn" class="btn btn-primary mt-3">Kirim Pertanyaan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi -->
    <div class="modal fade" id="konfirmasiModal" tabindex="-1" aria-labelledby="konfirmasiModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="konfirmasiModalLabel">Konfirmasi Pengiriman</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin mengirim pertanyaan?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="kirimPertanyaanBtn" class="btn btn-primary">Kirim Pertanyaan</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.getElementById('submitBtn').addEventListener('click', function () {
        const modal = new bootstrap.Modal(document.getElementById('konfirmasiModal'));
        modal.show();
    });

    document.getElementById('kirimPertanyaanBtn').addEventListener('click', function () {
        // Cukup kirim form ke route aslinya (kom.pertanyaan_peserta.store)
        document.getElementById('pertanyaanForm').submit();
    });
</script>

@endsection
