    @extends('layouts.kom')

    @section('content')
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Edit Rayon</h3>
                        <div class="card-tools">
                            <a href="{{ route('kom.rayon.index') }}" class="btn btn-sm btn-secondary">Kembali</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('kom.rayon.update', $rayon->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label for="name">Nama Rayon</label>
                                <input type="text" name="name" class="form-control" value="{{ $rayon->name }}"
                                    required>
                            </div>

                            <div class="form-group">
                                <label for="status">Status</label>
                                <select name="status" class="form-control">
                                    <option value="1" {{ $rayon->status ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ !$rayon->status ? 'selected' : '' }}>Tidak Aktif</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary">Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endsection
