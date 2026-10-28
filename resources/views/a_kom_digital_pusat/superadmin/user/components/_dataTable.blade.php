<!-- Form Mass Action -->
<form action="{{ route('kom.super_user.massAction') }}" method="POST" id="mass-action-form">
    @csrf
    @method('PUT')
    <input type="hidden" name="action" id="mass-action-input" value="">


    <!-- Table Users -->
    <div class="table-responsive b-0" data-pattern="priority-columns">
        <table id="tech-companies-1" class="table table-striped text-truncate">
            <thead class="thead-light">
                <tr>
                    <th>
                        <input type="checkbox" id="selectAll">
                    </th>
                    <th>#</th>
                    <th>NKP/NIK</th>
                    <th>Nama</th>
                    <!-- <th>Email</th> -->
                    <th>Asal Gereja</th>
                    <th>Cabang</th>
                    <th>Role</th>
                    <th>No SIM</th>
                    <th>Tanggal Daftar</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $user)
                    <tr>
                        <td>
                            <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="user-checkbox">
                        </td>
                        <td>{{ $loop->iteration + ($data->currentPage() - 1) * $data->perPage() }}</td>
                        <td>{{ $user->nik }}</td>
                        <td>{{ $user->name }}<br>
                            <small class="text-muted">{{ $user->email }}</small><br>
                            <small class="text-muted">{{ $user->id }}</small>
                        </td>
                        <!-- <td>{{ $user->email }}</td> -->
                        <td>{{ $user->asal_gereja }}</td>
                        <td>{{ $user->cabang->name ?? 'N/A' }}</td>
                        <td>{{ ucfirst($user->role) }}</td>
                        <td>
                            <small>
                                @if ($user->statactive === 'Active')
                                    <span class="badge bg-success text-white">{{ $user->statactive }}</span>
                                @else
                                    <span class="badge bg-info">{{ $user->statactive }}</span>
                                @endif
                            </small>
                            <br>
                            {{ $user->no_sim }}
                            <br>
                            <small class="text-muted">{{ $user->idnkp }}</small>

                        </td>
                        <td>{{ $user->created_at->format('d-m-Y') }}</td>
                        <td>
                            @php
                                $encryptedId = Crypt::encryptString($user->id);
                            @endphp
                            <form action="{{ route('kom.super_user.updateStatus', $encryptedId) }}" method="POST"
                                style="display:inline;">
                                @csrf
                                @method('PUT')
                                {{-- kom_v02 (perbaikan): status aktif HANYA jika 'aktif' (sama dengan pengecekan login).
                                     Dulu 'tidak_aktif' ikut tampil "aktif" karena teks apa pun dianggap benar. --}}
                                @php $statusAktif = in_array((string) $user->status, ['aktif', '1'], true); @endphp
                                <input type="hidden" name="status" value="{{ $statusAktif ? 'nonaktif' : 'aktif' }}">
                                <button type="submit"
                                    class="btn btn-sm {{ $statusAktif ? 'btn-success' : 'btn-secondary' }}"
                                    title="{{ $statusAktif ? 'Klik untuk menonaktifkan' : 'Klik untuk mengaktifkan' }}">
                                    {{ $statusAktif ? 'aktif' : 'nonaktif' }}
                                </button>
                            </form>
                        </td>
                        <td>
                            <div class="d-flex gap-2">

                                <a class="btn btn-sm btn-primary"
                                    href="{{ route('kom.super_user.show', Crypt::encryptString($user->id)) }}">
                                    Show
                                </a>

                                <a class="btn btn-sm btn-warning"
                                    href="{{ route('kom.super_user.edit', Crypt::encryptString($user->id)) }}">
                                    Edit
                                </a>

                                <form action="{{ route('kom.super_user.destroy', Crypt::encryptString($user->id)) }}"
                                    method="POST"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');"
                                    class="m-0 p-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        Hapus
                                    </button>
                                </form>

                                <!-- <form action="{{ route('kom.super_user.resetUser', Crypt::encryptString($user->id)) }}"
                                    method="POST"
                                    onsubmit="return confirm('Apakah Anda yakin ingin mereset data pengguna ini? Semua data terkait akan dihapus.');"
                                    class="m-0 p-0">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-secondary">
                                        Reset Data
                                    </button>
                                </form> -->

                            </div>
                        </td>

                        <!-- <td>
                                    <div class="dropdown">
                                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button"
                                            id="dropdownMenuButton{{ $user->id }}" data-bs-toggle="dropdown"
                                            data-bs-display="static" aria-expanded="false">
                                            &#x22EE;
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end"
                                            aria-labelledby="dropdownMenuButton{{ $user->id }}">
                                            <li>
                                                <a class="dropdown-item"
                                                    href="{{ route('kom.super_user.show', Crypt::encryptString($user->id)) }}">
                                                    Show
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item"
                                                    href="{{ route('kom.super_user.edit', Crypt::encryptString($user->id)) }}">
                                                    Edit
                                                </a>
                                            </li>
                                            <li>
                                                <form
                                                    action="{{ route('kom.super_user.destroy', Crypt::encryptString($user->id)) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');"
                                                    class="m-0 p-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </li>
                                            <li>
                                                <form
                                                    action="{{ route('kom.super_user.resetUser', Crypt::encryptString($user->id)) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Apakah Anda yakin ingin mereset data pengguna ini? Semua data terkait akan dihapus.');"
                                                    class="m-0 p-0">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item text-warning">
                                                        Reset Data
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td> -->
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center">Tidak ada pengguna ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</form>