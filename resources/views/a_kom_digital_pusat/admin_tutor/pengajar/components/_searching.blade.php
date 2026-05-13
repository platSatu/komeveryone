<form method="GET" action="" class="mb-3">
    <div class="row g-2 align-items-center">
        <div class="col-5">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari pengguna..."
                value="{{ request('search') }}">
        </div>

        <div class="col-5">
            <select name="role" class="form-control form-control-sm">
                <option value="">-- Semua Role --</option>
                <option value="siswa" {{ request('role') === 'siswa' ? 'selected' : '' }}>Siswa
                </option>
                <option value="pengajar" {{ request('role') === 'pengajar' ? 'selected' : '' }}>Pengajar
                </option>
                <option value="admin_cabang" {{ request('role') === 'admin_cabang' ? 'selected' : '' }}>
                    Admin Cabang</option>
                <option value="admin_kom" {{ request('role') === 'admin_kom' ? 'selected' : '' }}>Admin
                    Kom</option>
                <option value="superadmin" {{ request('role') === 'superadmin' ? 'selected' : '' }}>
                    Superadmin</option>
            </select>
        </div>

        <div class="col-2">
            <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
        </div>
    </div>
</form>
