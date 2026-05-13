@extends('layouts.KomDigital')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">

                    {{-- Form Search --}}
                    <form method="GET" action="{{ url()->current() }}" class="mb-3">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                                    placeholder="Cari User, Modul, Kelas, atau Cabang...">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary">Cari</button>
                                <a href="{{ url()->current() }}" class="btn btn-secondary">Reset</a>
                            </div>
                        </div>
                    </form>

                    {{-- Tabel --}}
                    <div class="table-responsive b-0" data-pattern="priority-columns">
                        <table id="tech-companies-1" class="table table-striped text-truncate">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>User</th>
                                    <th>Modul</th>
                                    <th>Kelas</th>
                                    <th>Cabang</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Transaction Time</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($donasi as $item)
                                    <tr>
                                        <td>{{ $loop->iteration + ($donasi->currentPage() - 1) * $donasi->perPage() }}</td>
                                        <td>{{ $item->user->name ?? '-' }}</td>
                                        <td>{{ $item->modul->name ?? '-' }}</td>
                                        <td>{{ $item->kelas->name ?? '-' }}</td>
                                        <td>{{ $item->cabang->name ?? '-' }}</td>
                                        <td>{{ $item->formatted_amount }}</td>
                                        <td><span
                                                class="badge {{ $item->status_badge_class }} text-white">{{ ucfirst($item->status) }}</span>
                                        </td>
                                        <td>{{ $item->transaction_time?->format('d M Y H:i') ?? '-' }}</td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('kom.super_persembahan.edit', Crypt::encrypt($item->id)) }}"
                                                    class="btn btn-sm btn-primary">Edit</a>

                                                <form
                                                    action="{{ route('kom.super_persembahan.destroy', Crypt::encrypt($item->id)) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                                </form>
                                            </div>
                                        </td>


                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Data tidak ditemukan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{-- Pagination --}}
                    <div class="mt-3">
                        {{ $donasi->appends(request()->query())->links('pagination::bootstrap-4') }}
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
