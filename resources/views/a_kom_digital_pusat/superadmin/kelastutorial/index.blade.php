@extends('layouts.KomDigital')
@section('content')
    <div class="row">
        <div class="col-md-12">
            {{-- Card filter & info --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
                    <form action="" method="GET" class="form-inline mb-2 mb-md-0 d-flex align-items-center">
                        <label for="rayon_id" class="mr-2 mb-0">Filter Rayon:</label>
                        <select name="rayon_id" id="rayon_id" class="form-control mr-2">
                            <option value="">-- Semua Rayon --</option>
                            @foreach ($rayons as $rayon)
                                <option value="{{ $rayon->id }}"
                                    {{ request('rayon_id') == $rayon->id ? 'selected' : '' }}>
                                    {{ $rayon->name }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm">Tampilkan</button>
                    </form>

                    <div>
                        <strong>Total Kelas Dibuka:</strong> {{ $data->total() }}
                    </div>
                </div>
            </div>

            {{-- Card tabel jadwal --}}
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Daftar Jadwal Kelas</h5>
                    <a href="{{ route('kom.super_kelas_tutorial.create') }}" class="btn btn-primary btn-sm">+ Tambah
                        Jadwal</a>
                </div>

                <div class="card-body">
                    @if ($data->count() > 0)
                        <div class="table-responsive b-0" data-pattern="priority-columns">
                            <table id="tech-companies-1" class="table table-striped text-truncate">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Admin Cabang</th>
                                        <th>Pengajar</th>
                                        <th>Rayon</th>
                                        <th>Topic</th>
                                        <th>Kelas</th>
                                        <th>Modul</th>
                                        
                                        <th>Waktu</th>
                                        <th>Quota</th>
                                        <th>Jumlah Ikut</th>
                                        <th>Sisa</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                        <th>Zoom</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $index => $item)
                                        <tr>
                                            <td>{{ $data->firstItem() + $index }}</td>
                                            <td>{{ $item->user->name ?? '-' }}</td>
                                            <td>{{ $item->pengajar->name ?? '-' }}</td>
                                            <td>{{ $item->rayon->name ?? '-' }}</td>
                                            <td>{{ $item->topic ?? '-' }}</td>
                                            <td>{{ $item->kelas->name ?? '-' }}</td>
                                            <td>{{ $item->modul->name ?? '-' }}</td>
                                            
                                            <td>
                                                {{ \Carbon\Carbon::parse($item->start_time)->format('d M Y H:i') ?? '-' }}
                                            </td>
                                            <td>{{ $item->quota ?? '-' }}</td>
                                            <td>{{ $item->jml_ikut ?? '-' }}</td>
                                            <td>{{ $item->sisa_quota ?? '-' }}</td>
                                            <td>
                                                @if ($item->status == 'active')
                                                    <span class="badge bg-success text-white">Aktif</span>
                                                @else
                                                    <span class="badge bg-secondary text-white">Nonaktif</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('kom.super_kelas_tutorial.edit', Crypt::encrypt($item->id)) }}"
                                                    class="btn btn-sm btn-warning">Edit</a>

                                                <form
                                                    action="{{ route('kom.super_kelas_tutorial.destroy', Crypt::encrypt($item->id)) }}"
                                                    method="POST" onsubmit="return confirm('Yakin ingin hapus?')"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-danger" type="submit">Hapus</button>
                                                </form>
                                            </td>
                                            <td class="text-center">
                                                @empty($item->zoom_meeting_id)
                                                    -
                                                @else
                                                    <a
                                                        href="{{ route('kom.super_kelas_tutorial.meetings.join', ['zoomMeetingId' => $item->zoom_meeting_id]) }}">
                                                        <i class="fas fa-video" style="color:#2D8CFF;"></i>&nbsp;Join Zoom
                                                    </a>
                                                @endempty

                                                {{-- <div class="d-flex align-items-center">
                                                    <span class="badge bg-light text-dark border px-3 py-2">

                                                        <a href="{{ route('kom.peserta.meetings.join', ['zoomMeetingId' => $data->zoom_meeting_id]) }}"
                                                            class="text-center"><i class="fas fa-video"
                                                                style="color:#2D8CFF;"></i>&nbsp;

                                                            Join Zoom
                                                        </a>
                                                    </span>

                                                </div> --}}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                         <div class="d-flex justify-content-center mt-3">
                        {{ $data->appends(['search' => request('search')])->links('pagination::bootstrap-4') }}
                    </div>
                    @else
                        <p class="text-center">Data tidak ditemukan.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
