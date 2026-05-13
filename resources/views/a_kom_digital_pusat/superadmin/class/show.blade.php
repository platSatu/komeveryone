@extends('layouts.KomDigital')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Detail Kelas KOM</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Nama Kelas:</strong></td>
                                    <td>{{ $classItem->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge {{ $classItem->status ? 'bg-success' : 'bg-danger' }}">
                                            {{ $classItem->status }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Dibuat:</strong></td>
                                    <td>{{ $classItem->created_at->format('d/m/Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Diupdate:</strong></td>
                                    <td>{{ $classItem->updated_at->format('d/m/Y H:i:s') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="mt-3">
                        <a href="{{ route('kom.super_kelas-kom.edit', $classItem->encrypted_id) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('kom.super_kelas-kom.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <form action="{{ route('kom.super_kelas-kom.destroy', $classItem->encrypted_id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus kelas {{ $classItem->name }}?')">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection