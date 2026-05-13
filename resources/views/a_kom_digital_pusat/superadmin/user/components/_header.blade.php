<div class="card-header d-flex justify-content-between align-items-center">
    <h4 class="card-title mb-0">Daftar User</h4>
    <div class="d-flex align-items-center gap-2">

        {{-- Form upload file import --}}
        {{-- <form action="{{ route('kom.super_user.import') }}" method="POST" enctype="multipart/form-data"
            class="d-flex align-items-center gap-2">
            @csrf
            <input type="file" name="file"
                accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                required class="form-control form-control-sm" style="width: 180px; padding: 0.25rem 0.5rem;">
            <button type="submit" class="btn btn-primary btn-sm mr-3">Import</button>
        </form> --}}

        <!-- Tombol tambah pengguna -->
        <a href="{{ route('kom.super_user.create') }}" class="btn btn-success btn-sm mr-3">Tambah User</a>

       

        <!-- Dropdown Aksi Massal -->
        {{-- <div class="dropdown">
            <button class="btn btn-warning btn-sm dropdown-toggle" type="button" id="dropdownMenuAksi"
                data-bs-toggle="dropdown" aria-expanded="false">
                Menu Aksi Massal
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuAksi">
                <li><a href="#" class="dropdown-item mass-action-btn" data-action="reset">Reset Data Terpilih</a>
                </li>
                <li><a href="#" class="dropdown-item mass-action-btn" data-action="delete">Hapus Terpilih</a></li>
                <li><a href="#" class="dropdown-item mass-action-btn" data-action="activate">Aktifkan Terpilih</a>
                </li>
                <li><a href="#" class="dropdown-item mass-action-btn" data-action="deactivate">Nonaktifkan
                        Terpilih</a></li>
            </ul>
        </div> --}}
    </div>
</div>
