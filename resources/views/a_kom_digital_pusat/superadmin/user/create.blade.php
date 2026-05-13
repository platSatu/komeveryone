@extends('layouts.KomDigital')

@section('content')
    <div class="row">
        <div class="col-12">
            <!-- @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Oops! Ada beberapa kesalahan:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li> {{-- Menampilkan pesan error per aturan validasi --}}
                        @endforeach
                    </ul>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif -->

            <div class="card">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Tambah User</h4>
                    <a href="{{ route('kom.super_user.index') }}" class="btn btn-primary">Kembali</a>
                </div>

                <div class="card-body">
                    <form action="{{ route('kom.super_user.store') }}" method="POST">
                        @csrf
                        <div class="table-responsive b-0" data-pattern="priority-columns">
                            <table id="users-table" class="table table-striped text-truncate">
                               
                                <thead>
                                    <tr>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Password</th>
                                        <th>Konfirmasi Password</th>
                                        <th>Rayon</th>
                                        <th>Role</th>
                                        <th>Asal Gereja</th>
                                        <th>Tahun Lahir</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="user-row">
                                        <td>
                                            <input type="text" name="users[0][name]" class="form-control" required>
                                        </td>
                                        <td>
                                            <input type="email" name="users[0][email]" class="form-control" required>
                                        </td>
                                        <td>
                                            <input type="password" name="users[0][password]" class="form-control" required>
                                        </td>
                                        <td>
                                            <input type="password" name="users[0][password_confirmation]"
                                                class="form-control" required>
                                        </td>
                                        <td>
                                            <select name="users[0][cabang_id]" class="form-control">
                                                @foreach ($rayons as $rayon)
                                                    <option value=""></option>
                                                    <option value="{{ $rayon->id }}">{{ $rayon->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select name="users[0][role]" class="form-control" required>
                                                <option value="peserta">Siswa</option>
                                                <option value="pengajar">Pengajar</option>
                                                <option value="admin_cabang">Admin Cabang</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" name="users[0][asal_gereja]" class="form-control">
                                        </td>
                                        <td>
                                            <input type="number" name="users[0][tahun_lahir]" class="form-control"
                                                min="1900" max="{{ date('Y') }}">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)"
                                                disabled>Hapus</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <button type="button" class="btn btn-primary btn-sm mb-3" id="add-row-btn">Tambah Baris</button>
                        <br>
                        <button type="submit" class="btn btn-success btn-sm">Simpan Semua</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let userIndex = 1; // Mulai dari 1 karena 0 sudah ada

        document.getElementById('add-row-btn').addEventListener('click', function() {
            const tableBody = document.querySelector('#users-table tbody');

            const newRow = document.createElement('tr');
            newRow.classList.add('user-row');

            newRow.innerHTML = `
            <td>
                <input type="text" name="users[${userIndex}][name]" class="form-control" required>
            </td>
            <td>
                <input type="email" name="users[${userIndex}][email]" class="form-control" required>
            </td>
            <td>
                <input type="password" name="users[${userIndex}][password]" class="form-control" required>
            </td>
            <td>
                <input type="password" name="users[${userIndex}][password_confirmation]" class="form-control" required>
            </td>
            <td>
                <select name="users[${userIndex}][cabang_id]" class="form-control">
                    @foreach ($rayons as $rayon)
                        <option value=""></option>
                        <option value="{{ $rayon->id }}">{{ $rayon->name }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <select name="users[${userIndex}][role]" class="form-control" required>
                    <option value="peserta">Siswa</option>
                    <option value="pengajar">Pengajar</option>
                    <option value="admin_cabang">Admin Cabang</option>
                </select>
            </td>
            <td>
                <input type="text" name="users[${userIndex}][asal_gereja]" class="form-control">
            </td>
            <td>
                <input type="number" name="users[${userIndex}][tahun_lahir]" class="form-control" min="1900" max="{{ date('Y') }}">
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">Hapus</button>
            </td>
        `;

            tableBody.appendChild(newRow);
            userIndex++;
            updateRemoveButtons();
        });

        function removeRow(button) {
            const rows = document.querySelectorAll('.user-row');
            if (rows.length > 1) {
                button.closest('tr').remove();
                updateRemoveButtons();
            }
        }

        function updateRemoveButtons() {
            const rows = document.querySelectorAll('.user-row');
            rows.forEach((row, index) => {
                const btn = row.querySelector('button.btn-danger');
                if (btn) {
                    btn.disabled = rows.length === 1; // Disable hapus jika hanya 1 baris tersisa
                }
            });
        }

        // Inisialisasi tombol hapus saat halaman load
        updateRemoveButtons();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    @if (session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Sukses',
            text: "{{ session('success') }}",
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: false,
        });
    @endif

    @if (session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Terjadi Kesalahan',
            text: "{{ session('error') }}",
            confirmButtonText: 'Tutup'
        });
    @endif

    @if ($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Terjadi Kesalahan',
            html: `
                <ul style="text-align: left;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            `,
            confirmButtonText: 'Tutup'
        });
    @endif
</script>
@endsection
