@extends('layouts.KomDigital')
@section('content')

<div class="container-fluid">
    {{-- Judul Halaman --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Rekening</h1>
        <a href="{{ route('kom.super_daftar-rekening.index') }}" class="btn btn-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white"></i> Kembali
        </a>
    </div>

    {{-- Card Form --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-edit"></i> Form Edit Rekening
            </h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('kom.super_daftar-rekening.update', $rekening->id) }}">
                @csrf
                @method('PUT')

                <div class="row">
                    {{-- Nama Rekening --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nama_rek">
                                Nama Rekening <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   name="nama_rek" 
                                   id="nama_rek" 
                                   class="form-control @error('nama_rek') is-invalid @enderror"
                                   value="{{ old('nama_rek', $rekening->nama_rek) }}"
                                   placeholder="Masukkan nama rekening"
                                   required>
                            @error('nama_rek')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Nomor Rekening --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="no_rek">
                                Nomor Rekening <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   name="no_rek" 
                                   id="no_rek" 
                                   class="form-control @error('no_rek') is-invalid @enderror"
                                   value="{{ old('no_rek', $rekening->no_rek) }}"
                                   placeholder="Masukkan nomor rekening"
                                   required>
                            @error('no_rek')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    {{-- Nama Bank --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nama_bank">
                                Nama Bank <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   name="nama_bank" 
                                   id="nama_bank" 
                                   class="form-control @error('nama_bank') is-invalid @enderror"
                                   value="{{ old('nama_bank', $rekening->nama_bank) }}"
                                   placeholder="Masukkan nama bank"
                                   required>
                            @error('nama_bank')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Cabang --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="cabang_id">
                                Cabang <span class="text-danger">*</span>
                            </label>
                            <select name="cabang_id" 
                                    id="cabang_id" 
                                    class="form-control @error('cabang_id') is-invalid @enderror"
                                    required>
                                <option value="">-- Pilih Cabang --</option>
                                @foreach($cabangs as $cabang)
                                    <option value="{{ $cabang->id }}" 
                                        {{ old('cabang_id', $rekening->cabang_id) == $cabang->id ? 'selected' : '' }}>
                                        {{ $cabang->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('cabang_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    {{-- User --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="user_id">
                                User <span class="text-danger">*</span>
                            </label>
                            <select name="user_id" 
                                    id="user_id" 
                                    class="form-control @error('user_id') is-invalid @enderror"
                                    required>
                                <option value="">-- Pilih User --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" 
                                        {{ old('user_id', $rekening->user_id) == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <hr class="mt-4">
                <div class="form-group text-right">
                    <a href="{{ route('kom.super_daftar-rekening.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection