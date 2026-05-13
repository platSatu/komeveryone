<table class="table table-bordered table-hover">
    <thead class="thead-light">
        <tr>
            <th>#</th>
            <th>Nama</th>
            <th>Email</th>
            <th>Cabang</th>
            <th>Role</th>
            <th>Tanggal Daftar</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($data as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->name }}</td>
                <td>{{ $item->email }}</td>
                <td>{{ $item->cabang->name ?? '-' }}</td>
                <td>{{ ucfirst($item->role) }}</td>
                <td>{{ $item->created_at->format('d M Y') }}</td>
                <td>
                    <span class="badge badge-{{ $item->status === 'aktif' ? 'success' : 'danger' }}">
                        {{ ucfirst($item->status) }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('kom.admin_cabang_users.edit', Crypt::encryptString($item->id)) }}"
                        class="btn btn-sm btn-warning">Edit</a>

                    <form action="{{ route('kom.admin_cabang_users.toggle_status', encrypt($item->id)) }}" method="POST"
                        style="display:inline;">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                            class="btn btn-sm btn-{{ $item->status === 'aktif' ? 'danger' : 'success' }}"
                            onclick="return confirm('Yakin ingin mengubah status user ini?');">
                            {{ $item->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                    </form>

                    <form action="{{ route('kom.admin_cabang_users.destroy', Crypt::encryptString($item->id)) }}"
                        method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger" type="submit">Hapus</button>
                    </form>

                    <form action="{{ route('kom.admin_cabang_users.detail', Crypt::encryptString($item->id)) }}"
                        method="GET" class="d-inline">
                        <button type="submit" class="btn btn-sm btn-info">
                            Detail
                        </button>
                    </form>


                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="text-center">Belum ada data.</td>
            </tr>
        @endforelse
    </tbody>
</table>
