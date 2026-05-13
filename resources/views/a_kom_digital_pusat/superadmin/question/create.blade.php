@extends('layouts.KomDigital')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Tambah Pertanyaan</h4>
                    <div class="card-tools">
                        <a href="{{ route('kom.super_pertanyaan.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
                    </div>
                </div>

                <form action="{{ route('kom.super_pertanyaan.store') }}" method="POST">
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

                        <div id="questions-container">
                            <div class="form-group question-row">
                                <label for="questions[]">Pertanyaan</label>
                                <textarea name="questions[]" class="form-control mb-2" rows="4" required>{{ old('questions.0') }}</textarea>
                                <button type="button" class="btn btn-danger btn-sm remove-question-btn" style="display:none;">Hapus</button>
                            </div>
                        </div>

                        <button type="button" id="add-question-btn" class="btn btn-success btn-sm mt-2">Tambah Pertanyaan</button>

                        <div class="form-group mt-4">
                            <label for="status">Status Modul</label>
                            <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="card-footer text-right">
                        <button type="submit" class="btn btn-primary">Simpan Pertanyaan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const addBtn = document.getElementById('add-question-btn');
            const container = document.getElementById('questions-container');

            addBtn.addEventListener('click', function () {
                // Buat elemen div baru untuk row pertanyaan
                const newRow = document.createElement('div');
                newRow.classList.add('form-group', 'question-row');

                // Buat label
                const label = document.createElement('label');
                label.textContent = 'Pertanyaan';

                // Buat textarea
                const textarea = document.createElement('textarea');
                textarea.name = 'questions[]';
                textarea.classList.add('form-control', 'mb-2');
                textarea.rows = 4;
                textarea.required = true;

                // Buat tombol hapus
                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.classList.add('btn', 'btn-danger', 'btn-sm', 'remove-question-btn');
                removeBtn.textContent = 'Hapus';

                // Event hapus row
                removeBtn.addEventListener('click', function () {
                    container.removeChild(newRow);
                    updateRemoveButtonsVisibility();
                });

                // Tambahkan elemen ke newRow
                newRow.appendChild(label);
                newRow.appendChild(textarea);
                newRow.appendChild(removeBtn);

                // Tambahkan newRow ke container
                container.appendChild(newRow);

                updateRemoveButtonsVisibility();
            });

            // Fungsi untuk sembunyikan tombol hapus jika hanya 1 row
            function updateRemoveButtonsVisibility() {
                const rows = container.querySelectorAll('.question-row');
                rows.forEach(row => {
                    const btn = row.querySelector('.remove-question-btn');
                    if (rows.length === 1) {
                        btn.style.display = 'none';
                    } else {
                        btn.style.display = 'inline-block';
                    }
                });
            }

            // Inisialisasi tombol hapus
            updateRemoveButtonsVisibility();
        });
    </script>
@endsection