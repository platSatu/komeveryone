@extends('layouts.KomDigital')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>Edit e-Book</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('kom.super_books.update', $book->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Pemilik Buku --}}
                    <div class="mb-3">
                        <label for="user_id" class="form-label">Pemilik Buku</label>
                        <select name="user_id" id="user_id" class="form-control @error('user_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Pemilik Buku --</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" {{ (old('user_id', $book->user_id) == $user->id) ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Kelas --}}
                    <div class="mb-3">
                        <label for="class_id" class="form-label">Kelas</label>
                        <select name="class_id" id="class_id" class="form-control @error('class_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach ($classes as $class)
                                <option value="{{ $class->id }}" {{ (old('class_id', $book->class_id) == $class->id) ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('class_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Sertifikat --}}
                    <div class="mb-3">
                        <label for="certificate_id" class="form-label">Sertifikat</label>
                        <select name="certificate_id" id="certificate_id" class="form-control @error('certificate_id') is-invalid @enderror">
                            <option value="">-- Pilih Sertifikat --</option>
                            @foreach ($certificates as $cert)
                                <option value="{{ $cert->id }}" {{ (old('certificate_id', $book->certificate_id) == $cert->id) ? 'selected' : '' }}>
                                    {{ $cert->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('certificate_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- No Buku --}}
                    <div class="mb-3">
                        <label for="no_books" class="form-label">Kode Buku</label>
                        <input type="text" name="no_books" id="no_books" class="form-control @error('no_books') is-invalid @enderror" value="{{ old('no_books', $book->no_books) }}" required>
                        @error('no_books')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tanggal Aktivasi --}}
                    <div class="mb-3">
                        <label for="date_publish" class="form-label">Tanggal Aktivasi</label>
                        <input type="date" name="date_publish" id="date_publish" class="form-control @error('date_publish') is-invalid @enderror" value="{{ old('date_publish', $book->date_publish ? $book->date_publish->format('Y-m-d') : '') }}" required>
                        @error('date_publish')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status_books" class="form-control @error('status') is-invalid @enderror" required>
                            <option value="">-- Pilih Status --</option>
                            <option value="active" {{ (old('status', $book->status) == 'active') ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ (old('status', $book->status) == 'inactive') ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tombol Submit --}}
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('kom.super_books.index') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection