@extends('layouts.kom')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Tambah Jawaban User</h3>
                <div class="card-tools">
                    <a href="{{ route('kom.user_jawaban_pertanyaan.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
                </div>
            </div>

            <form action="{{ route('kom.user_jawaban_pertanyaan.store') }}" method="POST">
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

                    <div class="form-group">
                        <label for="kom_user_id">User</label>
                        <select name="kom_user_id" class="form-control" required>
                            <option value="">-- Pilih User --</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" {{ old('kom_user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->username }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="pertanyaan_id">Pertanyaan</label>
                        <select name="pertanyaan_id" class="form-control" required>
                            <option value="">-- Pilih Pertanyaan --</option>
                            @foreach ($pertanyaans as $p)
                                <option value="{{ $p->id }}" {{ old('pertanyaan_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->pertanyaan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="jawaban_id">Jawaban</label>
                        <select name="jawaban_id" class="form-control" required>
                            <option value="">-- Pilih Jawaban --</option>
                            @foreach ($jawabans as $j)
                                <option value="{{ $j->id }}" {{ old('jawaban_id') == $j->id ? 'selected' : '' }}>
                                    {{ $j->jawaban }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group form-check">
                        <input type="checkbox" name="jawaban_benar" value="1" class="form-check-input" id="benar"
                            {{ old('jawaban_benar') ? 'checked' : '' }}>
                        <label for="benar" class="form-check-label">Jawaban Benar</label>
                    </div>
                </div>

                <div class="card-footer text-right">
                    <button class="btn btn-primary" type="submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
