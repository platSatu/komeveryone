@extends('layouts.kom')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tambah Modul KOM</h3>
                    <div class="card-tools">
                        <a href="{{ route('kom.modul.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
                    </div>
                </div>

                <form action="{{ route('kom.modul.store') }}" method="POST">
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
                            <label for="title">Judul Modul</label>
                            <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="description">Deskripsi</label>
                            <textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="order">Urutan Modul</label>
                            <input type="number" name="order" class="form-control" value="{{ old('order') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="kom_class_id">Pilih Kelas</label>
                            <select name="kom_class_id" class="form-control" required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach ($classes as $class)
                                    <option value="{{ $class->id }}"
                                        {{ old('kom_class_id') == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group form-check">
                            <input type="checkbox" name="status" value="1" class="form-check-input" id="status_modul"
                                {{ old('status', true) ? 'checked' : '' }}>
                            <label for="status" class="form-check-label">Aktifkan Modul</label>
                        </div>
                    </div>

                    <div class="card-footer text-right">
                        <button class="btn btn-primary" type="submit">Simpan Modul</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
