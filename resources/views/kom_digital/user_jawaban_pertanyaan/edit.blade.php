@extends('layouts.kom')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Edit Jawaban User</h3>
                <div class="card-tools">
                    <a href="{{ route('kom.user_jawaban_pertanyaan.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
                </div>
            </div>

            <form action="{{ route('kom.user_jawaban_pertanyaan.update', $jawabanUser->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">

                    <div class="form-group">
                        <label for="kom_user_id">User</label>
                        <select name="kom_user_id" class="form-control" required>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" {{ $jawabanUser->kom_user_id == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="pertanyaan_id">Pertanyaan</label>
                        <select name="pertanyaan_id" class="form-control" required>
                            @foreach ($pertanyaans as $p)
                                <option value="{{ $p->id }}" {{ $jawabanUser->pertanyaan_id == $p->id ? 'selected' : '' }}>
                                    {{ $p->pertanyaan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="jawaban_id">Jawaban</label>
                        <select name="jawaban_id" class="form-control" required>
                            @foreach ($jawabans as $j)
                                <option value="{{ $j->id }}" {{ $jawabanUser->jawaban_id == $j->id ? 'selected' : '' }}>
                                    {{ $j->jawaban }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group form-check">
                        <input type="checkbox" name="jawaban_benar" value="1" class="form-check-input" id="benar"
                            {{ $jawabanUser->jawaban_benar ? 'checked' : '' }}>
                        <label for="benar" class="form-check-label">Jawaban Benar</label>
                    </div>
                </div>

                <div class="card-footer text-right">
                    <button class="btn btn-primary" type="submit">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
