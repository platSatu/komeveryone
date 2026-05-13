@extends('layouts.kom')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Jawaban</h3>
                    <div class="card-tools">
                        <a href="{{ route('kom.jawaban.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
                    </div>
                </div>

                <form action="{{ route('kom.jawaban.update', $jawaban->id) }}" method="POST">
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
                            <label for="pertanyaan_id">Pertanyaan</label>
                            <select name="pertanyaan_id" class="form-control" required>
                                <option value="">-- Pilih Pertanyaan --</option>
                                @foreach ($pertanyaans as $pertanyaan)
                                    <option value="{{ $pertanyaan->id }}"
                                        {{ $jawaban->pertanyaan_id == $pertanyaan->id ? 'selected' : '' }}>
                                        {{ $pertanyaan->pertanyaan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="jawaban">Jawaban</label>
                            <input type="text" name="jawaban" class="form-control"
                                value="{{ old('jawaban', $jawaban->jawaban) }}" required>
                        </div>

                        <!-- HARUS ADA! Kirim nilai 0 kalau checkbox tidak dicentang -->
                         <div class="form-group form-check">
                            <input type="hidden" name="is_benar" value="0">

                            <!-- Checkbox: hanya override kalau dicentang -->
                            <input type="checkbox" name="is_benar" value="1" class="form-check-input" id="is_benar"
                                {{ old('is_benar', $jawaban->is_benar) == 1 ? 'checked' : '' }}>

                        </div>
                        <!-- Hidden input untuk memastikan 'is_benar' terkirim sebagai false jika checkbox tidak dicentang -->

                    </div>

                    <div class="card-footer text-right">
                        <button type="submit" class="btn btn-primary">Simpan Jawaban</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
