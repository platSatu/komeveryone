@extends('layouts.kom')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Relasi Video & Pertanyaan</h3>
                <div class="card-tools">
                    <a href="{{ route('kom.video_pertanyaan.create') }}" class="btn btn-primary btn-sm">+ Tambah Relasi</a>
                </div>
            </div>

            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Video</th>
                            <th>Pertanyaan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp

                            @foreach ($videoPertanyaan as $videoId => $items)
                                @php $rowspan = count($items); @endphp
                                @foreach ($items as $index => $item)
                                    <tr>
                                        @if ($index === 0)
                                            <td rowspan="{{ $rowspan }}">{{ $no++ }}</td>
                                            <td rowspan="{{ $rowspan }}">{{ $item->video->name ?? '-' }}</td>
                                        @endif
                                        <td>{{ $item->pertanyaan->pertanyaan ?? '-' }}</td>
                                         <td>
                                    <a href="{{ route('kom.video_pertanyaan.edit', $item->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                    <form action="{{ route('kom.video_pertanyaan.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm">Hapus</button>
                                    </form>
                                </td>
                                    </tr>
                                @endforeach
                            @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
