  <form method="GET" action="{{ route('kom.super_user.index') }}" class="mb-3">
        <div class="row g-2 align-items-end">
            <!-- Kolom Cari -->
            <div class="col-md-3">
                <label for="search" class="form-label mb-1">Cari User</label>
                <input type="text" name="search" id="search" class="form-control form-control-sm"
                    placeholder="Cari User..." value="{{ request('search') }}">
            </div>

            <!-- Kolom Rayon / CK -->
            <div class="col-md-3">
                <label for="select-rayon" class="form-label mb-1">Rayon / CK</label>
                <select class="form-control form-control-sm" id="select-rayon" name="asal_gereja" style="width: 100%;">
                    @if(request('asal_gereja'))
                        <option value="{{ request('asal_gereja') }}" selected>
                            {{ request('nama_rayon') ?? 'Rayon Terpilih' }}
                        </option>
                    @endif
                </select>
            </div>

            <!-- Kolom Role -->
            <div class="col-md-2">
                <label for="role" class="form-label mb-1">Role</label>
                <select name="role" id="role" class="form-control form-control-sm">
                    <option value="">-- Pilih Role --</option>
                    <option value="peserta" {{ request('role') === 'peserta' ? 'selected' : '' }}>Peserta</option>
                    <option value="pengajar" {{ request('role') === 'pengajar' ? 'selected' : '' }}>Pengajar</option>
                    <option value="admin_cabang" {{ request('role') === 'admin_cabang' ? 'selected' : '' }}>Admin Cabang</option>
                    <option value="superadmin" {{ request('role') === 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                </select>
            </div>

            <!-- Kolom Cabang -->
            <div class="col-md-2">
                <label for="cabang_id" class="form-label mb-1">Cabang</label>
                <select name="cabang_id" id="cabang_id" class="form-control form-control-sm">
                    <option value="">-- Pilih Cabang --</option>
                    @foreach ($cabangs as $cabang)
                        <option value="{{ $cabang->id }}" {{ request('cabang_id') == $cabang->id ? 'selected' : '' }}>
                            {{ $cabang->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Tombol Filter -->
            <div class="col-md-2 d-grid">
                <label class="form-label mb-1" style="visibility:hidden;">Filter</label>
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            </div>
        </div>
    </form>