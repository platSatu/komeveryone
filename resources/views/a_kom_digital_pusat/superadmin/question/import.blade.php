@extends('layouts.KomDigital')

@section('content')
    <div class="container">
        <h3>Import Pertanyaan dari Excel</h3>

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('kom.super_pertanyaan.import_excel') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="file" class="form-label">Pilih file Excel (.xlsx, .xls, .csv)</label>
                <input type="file" name="file" id="file" class="form-control" required>
                @error('file')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary">Import</button>
        </form>
    </div>
@endsection
