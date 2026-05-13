@extends('layouts.KomDigital')

@section('content')

    <h1 class="mb-4">Daftar Zoom Meeting</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <a href="{{ route('kom.zoom.create') }}" class="btn btn-primary mb-3">Buat Meeting Baru</a>

    <div class="card">
        <div class="card-body">
            @if($meetings->isEmpty())
                <p class="mb-0">Tidak ada meeting yang tersedia.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Topik</th>
                                <th scope="col">Waktu Mulai</th>
                                <th scope="col">Durasi (menit)</th>
                                <th scope="col">Host Email</th>
                                <th scope="col">Status</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($meetings as $index => $meeting)
                                <tr>
                                    <th scope="row">{{ $index + 1 }}</th>
                                    <td>{{ $meeting->topic }}</td>
                                    <td>{{ $meeting->start_time->format('d M Y H:i') }}</td>
                                    <td>{{ $meeting->duration ?? '-' }}</td>
                                    <td>{{ $meeting->host_email ?? '-' }}</td>
                                    <td>
                                        @if(strtolower($meeting->status) === 'waiting')
                                            <span class="badge bg-warning text-dark">Waiting</span>
                                        @elseif(strtolower($meeting->status) === 'started')
                                            <span class="badge bg-success">Started</span>
                                        @elseif(strtolower($meeting->status) === 'finished')
                                            <span class="badge bg-secondary">Finished</span>
                                        @else
                                            <span class="badge bg-info">{{ ucfirst($meeting->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('kom.zoom.zoom.meetings.join', $meeting->zoom_meeting_id) }}" class="btn btn-sm btn-primary">
                                            Gabung Meeting
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

@endsection