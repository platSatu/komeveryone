@extends('layouts.kom')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Edit Kelas KOM</h3>
                <div class="card-tools">
                    <a href="{{ route('kom.class.index') }}" class="btn btn-sm btn-secondary">Kembali</a>
                </div>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Ups!</strong> Ada masalah pada input Anda.<br><br>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('kom.class.update', $class->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="name">Nama Kelas</label>
                        <input type="text" name="name" class="form-control" value="{{ $class->name }}" required>
                    </div>

                    <div class="form-group">
                        <label for="status">Status Kelas</label>
                        <select name="status" class="form-control" required>
                            <option value="1" {{ $class->status == 1 ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ $class->status == 0 ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Perbarui</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
