@extends('layouts.KomDigital')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Tambah Jawaban</h3>
                <div class="card-tools">
                    <a href="{{ route('kom.super_jawaban.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
                </div>
            </div>

            <form action="{{ route('kom.super_jawaban.store') }}" method="POST" id="answer-form">
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

                    <div id="answers-container">
                        <div class="answer-row border p-3 mb-3">
                            <div class="form-group">
                                <label>Pertanyaan</label>
                                <select name="questions_id[0]" class="form-control" required>
                                    <option value="">-- Pilih Pertanyaan --</option>
                                    @foreach ($pertanyaans as $pertanyaan)
                                        <option value="{{ $pertanyaan->id }}"
                                            {{ old('questions_id.0') == $pertanyaan->id ? 'selected' : '' }}>
                                            {{ $pertanyaan->questions }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Jawaban</label>
                                <input type="text" name="answer[0]" class="form-control" value="{{ old('answer.0') }}" required>
                            </div>

                            <div class="form-group form-check">
                                <input type="checkbox" name="is_true[0]" value="1" class="form-check-input" id="is_true_0"
                                    {{ old('is_true.0') ? 'checked' : '' }}>
                                <label for="is_true_0" class="form-check-label">Jawaban Benar</label>
                            </div>

                            <div class="form-group">
                                <label>Status</label>
                                <select name="status[0]" class="form-control" required>
                                    <option value="">-- Pilih Status --</option>
                                    <option value="active" {{ old('status.0') == 'active' ? 'selected' : '' }}>Aktif</option>
                                    <option value="inactive" {{ old('status.0') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                                </select>
                            </div>

                            <button type="button" class="btn btn-danger btn-sm remove-answer-btn" style="display:none;">Hapus Jawaban</button>
                        </div>
                    </div>

                    <button type="button" id="add-answer-btn" class="btn btn-success btn-sm">Tambah Jawaban</button>

                </div>

                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-primary">Simpan Jawaban</button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const addBtn = document.getElementById('add-answer-btn');
    const container = document.getElementById('answers-container');

    function updateFieldNames() {
        const rows = container.querySelectorAll('.answer-row');
        rows.forEach((row, index) => {
            // Pertanyaan
            row.querySelector('select[name^="questions_id"]').name = `questions_id[${index}]`;

            // Jawaban
            row.querySelector('input[name^="answer"]').name = `answer[${index}]`;

            // Checkbox
            const checkbox = row.querySelector('input[type="checkbox"]');
            const label = row.querySelector('label.form-check-label');
            const newId = `is_true_${index}`;
            checkbox.name = `is_true[${index}]`;
            checkbox.id = newId;
            label.setAttribute('for', newId);

            // Status
            row.querySelector('select[name^="status"]').name = `status[${index}]`;
        });
    }

    function updateRemoveButtonsVisibility() {
        const rows = container.querySelectorAll('.answer-row');
        rows.forEach(row => {
            const btn = row.querySelector('.remove-answer-btn');
            btn.style.display = rows.length > 1 ? 'inline-block' : 'none';
        });
    }

    addBtn.addEventListener('click', function () {
        const firstRow = container.querySelector('.answer-row');
        const newRow = firstRow.cloneNode(true);

        newRow.querySelectorAll('input, select').forEach(input => {
            if (input.type === 'checkbox') {
                input.checked = false;
            } else {
                input.value = '';
            }
        });

        container.appendChild(newRow);
        updateFieldNames();
        updateRemoveButtonsVisibility();
    });

    container.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-answer-btn')) {
            e.preventDefault();
            const rows = container.querySelectorAll('.answer-row');
            if (rows.length > 1) {
                e.target.closest('.answer-row').remove();
                updateFieldNames();
                updateRemoveButtonsVisibility();
            }
        }
    });

    // Init saat load
    updateFieldNames();
    updateRemoveButtonsVisibility();
});
</script>
@endsection
