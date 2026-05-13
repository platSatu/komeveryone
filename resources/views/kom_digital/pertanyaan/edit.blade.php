@extends('layouts.kom')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Edit Pertanyaan</h3>
                <div class="card-tools">
                    <a href="{{ route('kom.pertanyaan.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
                </div>
            </div>

            <form action="{{ route('kom.pertanyaan.update', $pertanyaan->id) }}" method="POST">
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
                        <label for="pertanyaan">Pertanyaan</label>
                        <textarea name="pertanyaan" class="form-control" rows="4" required>{{ old('pertanyaan', $pertanyaan->pertanyaan) }}</textarea>
                    </div>
                </div>

                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-primary">Update Pertanyaan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
