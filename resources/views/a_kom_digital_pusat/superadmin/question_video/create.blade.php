@extends('layouts.KomDigital')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Setting Video di Pertanyaan</h4>
                <div class="card-tools">
                    <a href="{{ route('kom.super_pertanyaan-video.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
                </div>
            </div>

            <form action="{{ route('kom.super_pertanyaan-video.store') }}" method="POST" id="relation-form">
                @csrf
                <div class="card-body">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div id="relation-container">
                        <div class="relation-row border p-3 mb-3">
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Pilih Video</label>
                                    <select name="videos_id[0]" class="form-control" required>
                                        <option value="">-- Pilih Video --</option>
                                        @foreach ($videos as $video)
                                            <option value="{{ $video->id }}" {{ old('videos_id.0') == $video->id ? 'selected' : '' }}>
                                                {{ $video->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-4">
                                    <label>Pilih Pertanyaan</label>
                                    <select name="questions_id[0]" class="form-control" required>
                                        <option value="">-- Pilih Pertanyaan --</option>
                                        @foreach ($questions as $pertanyaan)
                                            <option value="{{ $pertanyaan->id }}" {{ old('questions_id.0') == $pertanyaan->id ? 'selected' : '' }}>
                                                {{ $pertanyaan->questions }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-3">
                                    <label>Status</label>
                                    <select name="status[0]" class="form-control" required>
                                        <option value="active" {{ old('status.0') == 'active' ? 'selected' : '' }}>Aktif</option>
                                        <option value="inactive" {{ old('status.0') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                                    </select>
                                </div>

                                <div class="form-group col-md-1 d-flex align-items-end">
                                    <button type="button" class="btn btn-danger btn-sm remove-relation-btn" style="display:none;">Hapus</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="button" id="add-relation-btn" class="btn btn-success btn-sm">Tambah Baris</button>

                </div>

                <div class="card-footer text-right">
                    <button class="btn btn-primary" type="submit">Simpan Relasi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const addBtn = document.getElementById('add-relation-btn');
    const container = document.getElementById('relation-container');

    function updateFieldNames() {
        const rows = container.querySelectorAll('.relation-row');
        rows.forEach((row, index) => {
            row.querySelector('select[name^="videos_id"]').name = `videos_id[${index}]`;
            row.querySelector('select[name^="questions_id"]').name = `questions_id[${index}]`;
            row.querySelector('select[name^="status"]').name = `status[${index}]`;

            // Tampilkan tombol hapus hanya jika lebih dari 1 baris
            const btn = row.querySelector('.remove-relation-btn');
            btn.style.display = rows.length > 1 ? 'inline-block' : 'none';
        });
    }

    addBtn.addEventListener('click', function () {
        const firstRow = container.querySelector('.relation-row');
        const newRow = firstRow.cloneNode(true);

        // Reset nilai input di baris baru
        newRow.querySelectorAll('select').forEach(select => {
            select.selectedIndex = 0;
        });

        container.appendChild(newRow);
        updateFieldNames();
    });

    container.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-relation-btn')) {
            e.preventDefault();
            const rows = container.querySelectorAll('.relation-row');
            if (rows.length > 1) {
                e.target.closest('.relation-row').remove();
                updateFieldNames();
            }
        }
    });

    // Inisialisasi saat load
    updateFieldNames();
});
</script>
@endsection