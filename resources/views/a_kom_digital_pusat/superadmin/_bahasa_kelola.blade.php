{{-- kom_v02: pita "Bahasa yang sedang dikelola" di halaman konten superadmin --}}
@php
    $__kelola = \App\Services\Kmd\BahasaKelolaService::current();
    $__semuaBahasa = \App\Models\Kmd\Language::orderBy('sort_order')->get();
    $__bahasaKelola = $__semuaBahasa->firstWhere('code', $__kelola);
@endphp
<div class="kom-kelola-bar">
    <div class="d-flex align-items-center flex-wrap" style="gap:10px">
        <i class="fas fa-language"></i>
        <span>{{ __('Anda sedang mengelola konten dalam') }}</span>
        <form method="POST" action="{{ route('kom.super_bahasa.kelola') }}" class="m-0">
            @csrf
            <select name="language_code" class="form-control form-control-sm" onchange="this.form.submit()">
                @foreach ($__semuaBahasa as $__l)
                    <option value="{{ $__l->code }}" {{ $__l->code === $__kelola ? 'selected' : '' }}>
                        {{ strtoupper($__l->code) }} — {{ $__l->native_name ?: $__l->name }}{{ $__l->is_active ? '' : ' (' . __('nonaktif') . ')' }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>
    <small>{{ __('Daftar dan data baru di halaman ini otomatis memakai bahasa tersebut.') }}
        <a href="{{ route('kom.super_bahasa.index') }}">{{ __('Kelola Bahasa') }}</a></small>
</div>
<style>
    .kom-kelola-bar { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;
        background:#eef3fc; border:1px solid #d5e0f5; border-left:4px solid #2a5298; border-radius:12px;
        padding:10px 16px; margin-bottom:16px; color:#1f2a44; }
    .kom-kelola-bar i { color:#2a5298; font-size:1.2rem; }
    .kom-kelola-bar select { min-width:220px; font-weight:600; }
    .kom-kelola-bar small { color:#5b6576; }
</style>
