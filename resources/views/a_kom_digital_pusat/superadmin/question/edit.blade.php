@extends('layouts.KomDigital')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Edit Pertanyaan</h4>
                    <div class="card-tools">
                        <a href="{{ route('kom.super_pertanyaan.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
                    </div>
                </div>

                <form action="{{ route('kom.super_pertanyaan.update', $question->id) }}" method="POST">
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

                        <div class="form-group">
                            <label for="questions">Pertanyaan</label>
                            <textarea name="questions" class="form-control @error('questions') is-invalid @enderror" rows="4" required>{{ old('questions', $question->questions) }}</textarea>
                            @error('questions')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mt-4">
                            <label for="status">Status Modul</label>
                            <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                                <option value="active" {{ old('status', $question->status) == 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="inactive" {{ old('status', $question->status) == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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