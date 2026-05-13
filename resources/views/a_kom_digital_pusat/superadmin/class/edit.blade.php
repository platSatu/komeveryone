@extends('layouts.KomDigital')

@section('content')
    <div class="row">
        <div class="col-12">
            {{-- Notifikasi Success --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Notifikasi Error --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Oops! Ada kesalahan pada input:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h4>Edit Kelas KOM</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('kom.super_kelas-kom.update', $classItem->encrypted_id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-3">
                            <label for="name">Nama Kelas</label>
                            <input type="text" id="name" name="name"
                                class="form-control @error('name') is-invalid @enderror" 
                                value="{{ old('name', $classItem->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="status">Status</label>
                            <select id="status_kelas" name="status"
                                class="form-control @error('status') is-invalid @enderror" required>
                                <option value="">-- Pilih Status --</option>
                                <option value="active" {{ old('status', $classItem->status) == 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="inactive" {{ old('status', $classItem->status) == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                            </select>

                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('kom.super_kelas-kom.index') }}" class="btn btn-secondary">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection