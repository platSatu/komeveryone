@extends('layouts.KomDigital')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Edit Jawaban</h3>
                <div class="card-tools">
                    <a href="{{ route('kom.super_jawaban.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
                </div>
            </div>

            <form action="{{ route('kom.super_jawaban.update', $answer->id) }}" method="POST" id="answer-form">
                @csrf
                @method('PUT')
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

                    <div class="form-group mb-3">
                        <label for="questions_id">Pertanyaan</label>
                        <select name="questions_id" id="questions_id" class="form-control" required>
                            <option value="">-- Pilih Pertanyaan --</option>
                            @foreach ($pertanyaans as $pertanyaan)
                                <option value="{{ $pertanyaan->id }}"
                                    {{ (old('questions_id', $answer->questions_id) == $pertanyaan->id) ? 'selected' : '' }}>
                                    {{ $pertanyaan->questions }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="answer">Jawaban</label>
                        <input type="text" name="answer" id="answer" class="form-control" 
                            value="{{ old('answer', $answer->answer) }}" required>
                    </div>

                    <div class="form-group form-check mb-3">
                        <input type="checkbox" name="is_true" value="1" class="form-check-input" id="is_true"
                            {{ old('is_true', $answer->is_true) ? 'checked' : '' }}>
                        <label for="is_true" class="form-check-label">Jawaban Benar</label>
                    </div>

                    <div class="form-group mb-3">
                        <label for="status">Status</label>
                        <select name="status" id="status_answer" class="form-control" required>
                            <option value="">-- Pilih Status --</option>
                            <option value="active" {{ old('status', $answer->status) == 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ old('status', $answer->status) == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>

                </div>

                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection