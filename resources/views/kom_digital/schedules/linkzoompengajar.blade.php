@extends('layouts.kom')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Daftar Link Zoom</h4>
                </div>

                <div class="card-body">
                    @if ($zoomLinks->isEmpty())
                        <div class="alert alert-info">
                            Tidak ada link Zoom tersedia untuk jadwal ini.
                        </div>
                    @else
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    
                                    <th>Link</th>
                                    <th>Tanggal Jadwal Kelas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($zoomLinks as $index => $zoom)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        
                                        <td>
                                            <a href="{{ $zoom->link }}" target="_blank" class="btn btn-primary btn-sm">
                                                Klik disini untuk Join Zoom
                                            </a>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($zoom->created_at)->format('d M Y H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif

                    <a href="{{ url()->previous() }}" class="btn btn-secondary mt-3">Kembali</a>
                </div>
            </div>
        </div>
    </div>
@endsection
